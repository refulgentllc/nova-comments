<?php

namespace KirschbaumDevelopment\NovaComments\Nova;

use Laravel\Nova\Resource;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Ebess\AdvancedNovaMediaLibrary\Fields\Files;
use Ebess\AdvancedNovaMediaLibrary\Fields\Images;
use KirschbaumDevelopment\NovaComments\Models\Comment as CommentModel;

class Comment extends Resource
{
     /**
     * Build an "index" query for the given resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if ($request->user()->isAdmin()) {
            return $query;
        }
    }
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = CommentModel::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'comment',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            Textarea::make('comment')
                ->alwaysShow()
                ->hideFromIndex(),

            MorphTo::make('Commentable')->onlyOnIndex(),

            Text::make('comment')
                ->displayUsing(function ($comment) {
                    return Str::limit($comment, config('nova-comments.limit'));
                })
                ->onlyOnIndex(),
            Boolean::make('Edited')->default(false),
            BelongsTo::make('Commenter', 'commenter', config('nova-comments.commenter.nova-resource'))
                ->exceptOnForms(),

            DateTime::make('Created', 'created_at')
                ->format(config('nova-comments.date-format'))
                ->exceptOnForms()
                ->sortable(),
            Images::make('Images', 'tickets-image')
                // ->conversionOnIndexView('thumb')
                ->enableExistingMedia()
                // ->croppingConfigs(['ratio' => 4/3])
                ,
            Files::make('Attachments', 'tickets-attach')
                // ->conversionOnIndexView('thumb')
                ->enableExistingMedia()
                // ->croppingConfigs(['ratio' => 4/3])
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function actions(Request $request)
    {
        return [];
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        return config('nova-comments.available-for-navigation');
    }
}
