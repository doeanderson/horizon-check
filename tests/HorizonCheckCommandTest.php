<?php

use DoeAnderson\HorizonCheck\Exceptions\HorizonNotRunningException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Exceptions;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

/**
 * @param  array<int, array{name: string, status: string}>  $masters
 */
function fakeHorizonMasters(array $masters): void
{
    $records = collect($masters)
        ->map(fn (array $master) => (object) [
            'name' => $master['name'],
            'environment' => 'testing',
            'pid' => 1234,
            'status' => $master['status'],
            'supervisors' => [],
        ])
        ->all();

    test()->mock(MasterSupervisorRepository::class)
        ->shouldReceive('all')
        ->andReturn($records);
}

beforeEach(fn () => Exceptions::fake());

it('succeeds when horizon:status reports it running', function () {
    fakeHorizonMasters([['name' => 'host-abc1', 'status' => 'running']]);

    $this->artisan('horizon:check')
        ->expectsOutputToContain('Horizon is running.')
        ->assertSuccessful();

    Exceptions::assertNothingReported();
});

it('reports an error when horizon:status finds no master supervisor', function () {
    fakeHorizonMasters([]);

    $this->artisan('horizon:check')
        ->expectsOutputToContain('Horizon is inactive.')
        ->assertFailed();

    Exceptions::assertReported(fn (HorizonNotRunningException $e) => str_contains($e->getMessage(), 'inactive'));
});

it('reports an error when horizon:status finds it paused', function () {
    fakeHorizonMasters([['name' => 'host-abc1', 'status' => 'paused']]);

    $this->artisan('horizon:check')
        ->expectsOutputToContain('Horizon is paused.')
        ->assertFailed();

    Exceptions::assertReported(fn (HorizonNotRunningException $e) => str_contains($e->getMessage(), 'paused'));
});

it('is scheduled every thirty minutes by default', function () {
    $event = collect(app(Schedule::class)->events())->firstWhere('description', 'horizon-check');

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('*/30 * * * *');
});

it('honours a custom schedule expression', function () {
    config(['horizon-check.schedule' => '0 * * * *']);

    $event = collect(app(Schedule::class)->events())->firstWhere('description', 'horizon-check');

    expect($event->expression)->toBe('0 * * * *');
});

it('skips scheduling when the schedule is disabled', function () {
    config(['horizon-check.schedule' => null]);

    $descriptions = collect(app(Schedule::class)->events())->pluck('description');

    expect($descriptions)->not->toContain('horizon-check');
});
