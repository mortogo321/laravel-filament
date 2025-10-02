<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('images')
                    ->label('Image')
                    ->circular()
                    ->stacked()
                    ->limit(3)
                    ->limitedRemainingText()
                    ->defaultImageUrl(url('/images/placeholder.svg')),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn ($record) => $record->sku)
                    ->wrap()
                    ->limit(30),

                TextColumn::make('category')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-tag')
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'success',
                        'archived' => 'danger',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft' => 'heroicon-m-pencil',
                        'active' => 'heroicon-m-check-circle',
                        'archived' => 'heroicon-m-archive-box',
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('price')
                    ->money('USD')
                    ->sortable()
                    ->alignEnd()
                    ->weight('bold')
                    ->color('success'),

                TextColumn::make('stock')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state < 10 => 'warning',
                        default => 'success',
                    }),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->toggleable(),

                IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean()
                    ->toggleable(),

                TextColumn::make('brand')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-m-user'),

                TextColumn::make('created_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'active' => 'Active',
                        'archived' => 'Archived',
                    ])
                    ->multiple()
                    ->label('Status'),

                SelectFilter::make('category')
                    ->options([
                        'Electronics' => 'Electronics',
                        'Clothing' => 'Clothing',
                        'Books' => 'Books',
                        'Toys' => 'Toys',
                        'Home & Garden' => 'Home & Garden',
                    ])
                    ->multiple(),

                TernaryFilter::make('is_featured')
                    ->label('Featured')
                    ->placeholder('All products')
                    ->trueLabel('Featured only')
                    ->falseLabel('Not featured'),

                TernaryFilter::make('is_visible')
                    ->label('Visibility')
                    ->placeholder('All products')
                    ->trueLabel('Visible only')
                    ->falseLabel('Hidden only'),

                Filter::make('out_of_stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '=', 0))
                    ->toggle()
                    ->label('Out of Stock'),

                Filter::make('low_stock')
                    ->query(fn (Builder $query): Builder => $query->where('stock', '>', 0)->where('stock', '<', 10))
                    ->toggle()
                    ->label('Low Stock'),

                Filter::make('published_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('published_from')
                            ->label('Published from'),
                        \Filament\Forms\Components\DatePicker::make('published_until')
                            ->label('Published until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),

                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ActionGroup::make([
                    Action::make('feature')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->action(function ($record) {
                            $record->update(['is_featured' => !$record->is_featured]);
                            Notification::make()
                                ->title($record->is_featured ? 'Product featured' : 'Product unfeatured')
                                ->success()
                                ->send();
                        }),
                    Action::make('toggleVisibility')
                        ->label('Toggle Visibility')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->action(function ($record) {
                            $record->update(['is_visible' => !$record->is_visible]);
                            Notification::make()
                                ->title($record->is_visible ? 'Product visible' : 'Product hidden')
                                ->success()
                                ->send();
                        }),
                    Action::make('duplicate')
                        ->icon('heroicon-m-document-duplicate')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $newProduct = $record->replicate();
                            $newProduct->name = $record->name . ' (Copy)';
                            $newProduct->slug = $record->slug . '-copy-' . time();
                            $newProduct->sku = $record->sku . '-COPY';
                            $newProduct->save();

                            Notification::make()
                                ->title('Product duplicated')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                    \Filament\Actions\BulkAction::make('feature')
                        ->label('Feature Selected')
                        ->icon('heroicon-m-star')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_featured' => true]);
                            Notification::make()
                                ->title('Products featured')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    \Filament\Actions\BulkAction::make('hide')
                        ->label('Hide Selected')
                        ->icon('heroicon-m-eye-slash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update(['is_visible' => false]);
                            Notification::make()
                                ->title('Products hidden')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped()
            ->poll('30s');
    }
}
