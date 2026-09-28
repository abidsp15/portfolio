<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->timestamps();
        });

        // Migrate existing project images into the new project_images table
        try {
            $projects = DB::table('projects')->whereNotNull('image_path')->where('image_path', '!=', '')->get();
            foreach ($projects as $project) {
                DB::table('project_images')->insert([
                    'project_id' => $project->id,
                    'image_path' => $project->image_path,
                    'created_at' => $project->created_at ?? now(),
                    'updated_at' => $project->updated_at ?? now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log warning or handle gracefully in case projects table is not accessible
            logger()->warning('Failed to migrate existing project images: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_images');
    }
};
