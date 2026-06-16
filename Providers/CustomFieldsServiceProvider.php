<?php

namespace Modules\CustomFields\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\CustomFields\Entities\ConversationCustomField;
use Modules\CustomFields\Entities\CustomField;

class CustomFieldsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'customfields');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'customfields');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->hooks();
    }

    public function register()
    {
    }

    public function hooks()
    {
        \Eventy::addFilter('stylesheets', function ($styles) {
            $styles[] = asset('modules/customfields/css/customfields.css');
            return $styles;
        });
        \Eventy::addFilter('javascripts', function ($scripts) {
            $scripts[] = asset('modules/customfields/js/customfields.js');
            return $scripts;
        });

        // Lien admin dans le menu "Manage" (admin only).
        \Eventy::addAction('menu.manage.append', function () {
            if (auth()->check() && auth()->user()->isAdmin()) {
                echo '<li><a href="' . route('customfields.index') . '">' . __('Custom Fields') . '</a></li>';
            }
        });

        // Bloc éditable dans la sidebar de conversation.
        \Eventy::addAction('conversation.after_subject', function ($conversation, $mailbox) {
            $fields = CustomField::orderBy('sort_order')->orderBy('id')->get();
            if (!$fields->count()) {
                return;
            }
            $values = ConversationCustomField::where('conversation_id', $conversation->id)
                ->pluck('value', 'custom_field_id')->toArray();
            echo \View::make('customfields::sidebar', [
                'conversation' => $conversation,
                'fields'       => $fields,
                'values'       => $values,
            ])->render();
        }, 20, 2);
    }
}
