<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTicketsSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('ticket_categories', function ($table) {
            $table->increments('id');
            $table->text('name');
            $table->string('key', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tickets', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('client_id')->nullable();
            $table->unsignedInteger('agent_id');
            $table->unsignedInteger('public_id');
            $table->unsignedInteger('priority_id')->default(1);
            $table->boolean('is_deleted')->default(0);
            $table->boolean('is_internal')->default(0);
            $table->unsignedInteger('status_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('merged_parent_ticket_id')->nullable();
            $table->unsignedInteger('parent_ticket_id')->nullable();
            $table->unsignedInteger('ticket_number');
            $table->text('subject');
            $table->text('description');
            $table->longtext('tags');
            $table->longtext('private_notes');
            $table->longtext('ccs');
            $table->string('ip_address', 255);
            $table->string('contact_key', 255);
            $table->dateTime('due_date');
            $table->dateTime('closed');
            $table->dateTime('reopened');
            $table->boolean('overdue_notification_sent')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
           // $table->foreign('status_id')->references('id')->on('ticket_statuses');


        });



        Schema::create('ticket_relations', function ($table) {
            $table->increments('id');
            $table->string('entity', 255);
            $table->unsignedInteger('entity_id');
            $table->unsignedInteger('ticket_id');
            $table->text('entity_url');

            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');

        });

        Schema::create('ticket_templates', function ($table) {
            $table->increments('id');
            $table->string('name', 255);
            $table->text('description');
            $table->unsignedInteger('account_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('public_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('ticket_comments', function ($table) {
            $table->increments('id');
            $table->text('description');
            $table->string('contact_key', 255);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('agent_id')->nullable();
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('ticket_id');
            $table->unsignedInteger('public_id');
            $table->boolean('is_deleted')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        });

        Schema::table('documents', function ($table) {
            $table->unsignedInteger('ticket_id')->nullable();
        });


        Schema::table('activities', function ($table) {
            $table->unsignedInteger('ticket_id')->nullable();

            $table->index(['ticket_id', 'account_id']);
        });


        Schema::create('ticket_invitations', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('contact_id');
            $table->unsignedInteger('ticket_id')->index();
            $table->string('invitation_key')->index()->unique();
            $table->string('ticket_hash')->index()->unique();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('sent_date')->nullable();
            $table->timestamp('viewed_date')->nullable();
            $table->timestamp('opened_date')->nullable();
            $table->string('message_id')->nullable();
            $table->text('email_error')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');

            $table->unsignedInteger('public_id')->index();
            $table->unique(['account_id', 'public_id']);
        });

        Schema::create('lookup_ticket_invitations', function ($table) {
            $table->increments('id');
            $table->unsignedInteger('lookup_account_id')->index();
            $table->string('invitation_key')->unique();
            $table->string('ticket_hash')->unique();
            $table->string('message_id')->nullable()->unique();

            $table->foreign('lookup_account_id')->references('id')->on('lookup_accounts')->onDelete('cascade');
        });

        Schema::create('account_ticket_settings', function ($table){
            $table->increments('id');
            $table->unsignedInteger('account_id')->index();
            $table->timestamps();

            $table->string('support_email_local_part')->unique()->nullable(); //allows a user to specify a custom *@support.invoiceninja.com domain
            $table->string('from_name', 255); //define the from email addresses name

            $table->boolean('client_upload')->default(true);
            $table->unsignedInteger('max_file_size')->default(0);
            $table->string('mime_types');

            $table->unsignedInteger('ticket_master_id');
            $table->unsignedInteger('default_agent_id')->nullable();

            $table->unsignedInteger('new_ticket_template_id');
            $table->unsignedInteger('close_ticket_template_id');
            $table->unsignedInteger('update_ticket_template_id');

            $table->unsignedInteger('default_priority');
            $table->string('ticket_number_prefix');
            $table->unsignedInteger('ticket_number_start');

            $table->unsignedInteger('alert_new_comment_id')->default(0);
            $table->longtext('alert_new_comment_id_email');
            $table->unsignedInteger('alert_ticket_assign_agent_id')->default(0);
            $table->longtext('alert_ticket_assign_email');
            $table->unsignedInteger('alert_ticket_overdue_agent_id')->default(0);
            $table->longtext('alert_ticket_overdue_email');

            $table->boolean('show_agent_details')->default(true);
            $table->string('postmark_api_token');

            $table->boolean('allow_inbound_email_tickets_external')->default(1);

            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('ticket_master_id')->references('id')->on('users')->onDelete('cascade');
        });


        Schema::table('lookup_accounts', function ($table) {
            $table->string('support_email_local_part')->unique()->nullable();
        });


        Schema::table('users', function ($table) {
            $table->string('avatar', 255);
            $table->unsignedInteger('avatar_width');
            $table->unsignedInteger('avatar_height');
            $table->unsignedInteger('avatar_size');
            $table->text('signature');
        });

        if(!Utils::isNinja()) {

            Schema::table('activities', function ($table) {
                $table->index(['contact_id', 'account_id']);
                $table->index(['payment_id', 'account_id']);
                $table->index(['invitation_id', 'account_id']);
                $table->index(['user_id', 'account_id']);
                $table->index(['invoice_id', 'account_id']);
                $table->index(['client_id', 'account_id']);
            });


            Schema::table('invitations', function ($table) {
                $table->index(['deleted_at', 'invoice_id']);
            });
        }


        /*
         *
         * Migrate all accounts
         *
         *
         * */



        $ticketCategory = new \App\Models\TicketCategory();
        $ticketCategory->name = 'Support';
        $ticketCategory->key = 'support';
        $ticketCategory->save();


        $accounts = \App\Models\Account::all();

        foreach ($accounts as $account){

            if(!$account->account_ticket_settings) {


                /* Create account_ticket_settings record for account */

                $user = $account->users()->whereIsAdmin(1)->first();

                $accountTicketSettings = new \App\Models\AccountTicketSettings();
                $accountTicketSettings->ticket_master_id = $user->id;

                $account->account_ticket_settings()->save($accountTicketSettings);


                /* Create ticket status */

                $ticketStatus = new \App\Models\TicketStatus();
                $ticketStatus->name = trans('texts.new');
                $ticketStatus->color = '#fff';
                $ticketStatus->description = 'Newly created ticket.';
                $ticketStatus->category_id = $ticketCategory->id;
                $ticketStatus->sort_order = 1;
                $ticketStatus->is_deleted = 0;
                $ticketStatus->public_id = 1;
                $ticketStatus->user_id = $account->user_id;
                $ticketStatus->account_id = $account->id;

                $ticketStatus->save();


                $ticketStatus = new \App\Models\TicketStatus();
                $ticketStatus->name = trans('texts.open');
                $ticketStatus->color = '#fff';
                $ticketStatus->description = 'Open ticket - replied.';
                $ticketStatus->category_id = $ticketCategory->id;
                $ticketStatus->sort_order = 2;
                $ticketStatus->is_deleted = 0;
                $ticketStatus->public_id = 2;
                $ticketStatus->user_id = $account->user_id;
                $ticketStatus->account_id = $account->id;

                $ticketStatus->save();



                $ticketStatus = new \App\Models\TicketStatus();
                $ticketStatus->name = trans('texts.closed');
                $ticketStatus->color = '#fff';
                $ticketStatus->description = 'Closed ticket - resolved.';
                $ticketStatus->category_id = $ticketCategory->id;
                $ticketStatus->sort_order = 3;
                $ticketStatus->is_deleted = 0;
                $ticketStatus->public_id = 3;
                $ticketStatus->user_id = $account->user_id;
                $ticketStatus->account_id = $account->id;

                $ticketStatus->save();



                $ticketStatus = new \App\Models\TicketStatus();
                $ticketStatus->name = trans('texts.merged');
                $ticketStatus->color = '#fff';
                $ticketStatus->description = 'Merged ticket.';
                $ticketStatus->category_id = $ticketCategory->id;
                $ticketStatus->sort_order = 4;
                $ticketStatus->is_deleted = 0;
                $ticketStatus->public_id = 4;
                $ticketStatus->user_id = $account->user_id;
                $ticketStatus->account_id = $account->id;

                $ticketStatus->save();


            }

        }



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ticket_statuses');
        Schema::dropIfExists('ticket_categories');
        Schema::dropIfExists('ticket_templates');
        Schema::dropIfExists('ticket_relations');
        Schema::dropIfExists('ticket_comments');
        Schema::dropIfExists('account_ticket_settings');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('lookup_ticket_invitations');
        Schema::dropIfExists('ticket_invitations');
    }
}
