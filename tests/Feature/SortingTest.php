<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Skill;
use App\Models\Education;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_skill_auto_increment_scopes_by_category_for_the_logged_in_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Create first Technical skill without sort order. Should be 1.
        $this->post(route('admin.skills.store'), [
            'name' => 'Laravel',
            'category' => 'Technical',
        ]);
        $this->assertDatabaseHas('skills', [
            'user_id' => $user->id,
            'name' => 'Laravel',
            'category' => 'Technical',
            'sort_order' => 1,
        ]);

        // 2. Create second Technical skill without sort order. Should be 2.
        $this->post(route('admin.skills.store'), [
            'name' => 'Vue',
            'category' => 'Technical',
        ]);
        $this->assertDatabaseHas('skills', [
            'user_id' => $user->id,
            'name' => 'Vue',
            'category' => 'Technical',
            'sort_order' => 2,
        ]);

        // 3. Create first Language skill without sort order. Should start at 1.
        $this->post(route('admin.skills.store'), [
            'name' => 'English',
            'category' => 'Language',
        ]);
        $this->assertDatabaseHas('skills', [
            'user_id' => $user->id,
            'name' => 'English',
            'category' => 'Language',
            'sort_order' => 1,
        ]);
    }

    public function test_skill_category_change_resets_sort_order_if_not_manually_specified()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Technical skills: Laravel (1), Vue (2)
        $laravel = Skill::create([
            'user_id' => $user->id,
            'name' => 'Laravel',
            'category' => 'Technical',
            'sort_order' => 1,
        ]);
        $vue = Skill::create([
            'user_id' => $user->id,
            'name' => 'Vue',
            'category' => 'Technical',
            'sort_order' => 2,
        ]);

        // Language skills: English (1)
        Skill::create([
            'user_id' => $user->id,
            'name' => 'English',
            'category' => 'Language',
            'sort_order' => 1,
        ]);

        // Update Vue (Technical, sort_order 2) -> Soft Skill.
        // It should auto-increment to 1 because there are no Soft Skills yet.
        $this->put(route('admin.skills.update', $vue->id), [
            'name' => 'Vue',
            'category' => 'Soft Skill',
            'sort_order' => 2, // submitted unchanged (i.e. same as existing Vue->sort_order)
        ]);

        $this->assertDatabaseHas('skills', [
            'id' => $vue->id,
            'category' => 'Soft Skill',
            'sort_order' => 1,
        ]);

        // Update Laravel (Technical, sort_order 1) -> Language.
        // It should auto-increment to 2 because English has sort_order 1 in Language.
        $this->put(route('admin.skills.update', $laravel->id), [
            'name' => 'Laravel',
            'category' => 'Language',
            'sort_order' => 1, // submitted unchanged
        ]);

        $this->assertDatabaseHas('skills', [
            'id' => $laravel->id,
            'category' => 'Language',
            'sort_order' => 2,
        ]);
    }

    public function test_skill_manual_sort_order_is_respected_and_does_not_get_overwritten()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create Technical skill with manual sort order of 5
        $this->post(route('admin.skills.store'), [
            'name' => 'Laravel',
            'category' => 'Technical',
            'sort_order' => 5,
        ]);

        $this->assertDatabaseHas('skills', [
            'user_id' => $user->id,
            'name' => 'Laravel',
            'category' => 'Technical',
            'sort_order' => 5,
        ]);

        // Next automatic skill should continue from 5 -> 6
        $this->post(route('admin.skills.store'), [
            'name' => 'Vue',
            'category' => 'Technical',
        ]);

        $this->assertDatabaseHas('skills', [
            'name' => 'Vue',
            'sort_order' => 6,
        ]);
    }

    public function test_updating_sort_order_to_blank_recalculates_to_max_plus_one()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $edu1 = Education::create([
            'user_id' => $user->id,
            'institution' => 'Uni A',
            'degree' => 'B.S.',
            'start_date' => '2018',
            'end_date' => '2022',
            'sort_order' => 1,
        ]);

        $edu2 = Education::create([
            'user_id' => $user->id,
            'institution' => 'Uni B',
            'degree' => 'M.S.',
            'start_date' => '2022',
            'end_date' => '2024',
            'sort_order' => 2,
        ]);

        // Update Uni A: submit sort_order as null/empty
        $this->put(route('admin.education.update', $edu1->id), [
            'institution' => 'Uni A Updated',
            'degree' => 'B.S.',
            'start_date' => '2018',
            'end_date' => '2022',
            'sort_order' => '', // cleared / empty
        ]);

        // Uni A should be recalculated to max + 1. The max was 2 (Uni B), so Uni A becomes 3.
        $this->assertDatabaseHas('education', [
            'id' => $edu1->id,
            'institution' => 'Uni A Updated',
            'sort_order' => 3,
        ]);
    }
}
