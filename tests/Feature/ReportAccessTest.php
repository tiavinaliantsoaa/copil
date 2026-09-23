<?php

namespace Tests\Feature;

use App\Models\CommunicationReport;
use App\Models\Period;
use App\Models\User;
use App\Services\ReportBlueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_communication_owner_can_update_an_open_report(): void
    {
        [$period, $report] = $this->report('open');
        $user = User::factory()->create(['role' => 'communication_owner']);

        $this->actingAs($user)->put(route('reports.sections.update', [$report, 'meta']), [
            'meta' => ['owner' => 'Équipe Communication', 'due_date' => '2026-10-03', 'status' => 'En cours'],
            '_panel' => 'dashboard',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('communication_reports', ['id' => $report->id, 'owner' => 'Équipe Communication']);
    }

    public function test_viewer_cannot_update_a_report(): void
    {
        [, $report] = $this->report('open');
        $viewer = User::factory()->create(['role' => 'viewer']);

        $this->actingAs($viewer)->put(route('reports.sections.update', [$report, 'meta']), [
            'meta' => ['owner' => 'Interdit', 'due_date' => null, 'status' => 'En cours'],
        ])->assertForbidden();
    }

    public function test_closed_report_is_locked_for_communication_owner_but_not_admin(): void
    {
        [, $report] = $this->report('closed');
        $owner = User::factory()->create(['role' => 'communication_owner']);
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = ['meta' => ['owner' => 'Correction admin', 'due_date' => null, 'status' => 'Terminé']];

        $this->actingAs($owner)->put(route('reports.sections.update', [$report, 'meta']), $payload)->assertStatus(423);
        $this->actingAs($admin)->put(route('reports.sections.update', [$report, 'meta']), $payload)->assertSessionHasNoErrors();
    }

    public function test_dashboard_renders_when_a_period_exists(): void
    {
        [$period] = $this->report('open');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('copil.index'))
            ->assertOk()
            ->assertSee($period->label)
            ->assertSee('COPIL Communication');
    }

    private function report(string $status): array
    {
        $period = Period::create([
            'key' => '2026-09', 'label' => 'Septembre 2026', 'starts_on' => '2026-09-01', 'status' => $status,
        ]);
        $report = CommunicationReport::create(array_merge(ReportBlueprint::report($period), ['period_id' => $period->id]));
        return [$period, $report];
    }
}
