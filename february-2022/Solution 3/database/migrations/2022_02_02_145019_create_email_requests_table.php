<?php

use App\Models\EmailRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('sender');
            $table->string('recipient');
            $table->text('message');
            $table->enum('status', [EmailRequest::SENT, EmailRequest::FAILED, EmailRequest::PROCESSING]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_requests');
    }
}
