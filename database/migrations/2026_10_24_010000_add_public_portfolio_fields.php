<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable();
            $table->string('resume_path')->nullable();
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->string('display_period')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
        });
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('seed_key')->nullable()->unique();
            $table->string('image')->nullable();
            $table->string('brand')->nullable();
        });
        Schema::table('education', function (Blueprint $table) {
            $table->string('seed_key')->nullable()->unique();
            $table->json('courses')->nullable();
            $table->json('achievements')->nullable();
            $table->string('image')->nullable();
        });
        Schema::table('skills', function (Blueprint $table) {
            $table->string('seed_key')->nullable()->unique();
            $table->string('group_name')->default('Additional skills');
            $table->string('group_description')->nullable();
            $table->unsignedInteger('group_order')->default(100);
            $table->unsignedInteger('sort_order')->default(0);
        });
        Schema::table('projects', function (Blueprint $table) {
            $table->string('seed_key')->nullable()->unique();
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->text('body')->nullable();
            $table->text('context')->nullable();
            $table->json('technologies')->nullable();
            $table->string('image')->nullable();
            $table->string('brand')->nullable();
            $table->string('link_label')->nullable();
            $table->boolean('featured')->default(true);
            $table->boolean('isShown')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
        });
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['seed_key', 'user_id', 'body', 'context', 'technologies', 'image', 'brand', 'link_label', 'featured', 'isShown', 'sort_order']);
        });
        Schema::table('skills', fn (Blueprint $table) => $table->dropColumn(['seed_key', 'group_name', 'group_description', 'group_order', 'sort_order']));
        Schema::table('education', fn (Blueprint $table) => $table->dropColumn(['seed_key', 'courses', 'achievements', 'image']));
        Schema::table('experiences', fn (Blueprint $table) => $table->dropColumn(['seed_key', 'image', 'brand']));
        Schema::table('sections', fn (Blueprint $table) => $table->dropColumn(['display_period', 'sort_order']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['roles', 'resume_path']));
    }
};
