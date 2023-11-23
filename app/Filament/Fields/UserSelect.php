<?php

namespace App\Filament\Fields;

use App\Models\User;
use Filament\Forms\Components\Select;

class UserSelect extends Select
{
    protected function setUp(): void
    {
        parent::setUp();

        // Set the options using a callback to format them
        $this->options(function () {
            return User::all()->pluck('name', 'id')->mapWithKeys(function ($name, $id) {
                /** @var User $user */
                $user = User::find($id);
                $thumbnailUrl = "https://eu.ui-avatars.com/api/?name=$user->name";

                // Format the option with an image and text
                // $optionHtml = "<img src='https://eu.ui-avatars.com/api/?name=kenneth' style='height: 20px; width: 20px; object-fit: cover; border-radius: 50%; margin-right: 5px;' /> {$name}";
                $optionHtml = "<p class=bold>test</p>";
                return [$id => $optionHtml];
            });
        });

        // Override the render method for options
        $this->getOptionLabelUsing(function ($value) {
            $user = User::find($value);
            if (!$user) {
                return $value;
            }

            $thumbnailUrl = "https://eu.ui-avatars.com/api/?name={{ $user->name }}";
            return "<img src='{$thumbnailUrl}' style='height: 20px; width: 20px; object-fit: cover; border-radius: 50%; margin-right: 5px;' /> {$user->name}";
        });
    }
}
