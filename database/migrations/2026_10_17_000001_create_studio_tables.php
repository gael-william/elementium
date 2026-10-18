<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('category')->default('general')->index();
            $table->string('video_type'); // youtube | vimeo | file | url
            $table->string('url');
            $table->string('thumbnail')->nullable();
            $table->unsignedInteger('duration')->default(0); // secondes
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->boolean('is_published')->default(false)->index();
            $table->boolean('is_featured')->default(false);
            $table->string('submitter_name')->nullable();
            $table->string('submitter_email')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('category')->index(); // animation | illustration | audio | gaming
            $table->string('image');
            $table->string('link')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->string('element')->default('feu'); // feu | eau | terre : couleur d'accent de la carte
            $table->json('socials')->nullable();
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('service')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('portfolio_items');
        Schema::dropIfExists('videos');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
