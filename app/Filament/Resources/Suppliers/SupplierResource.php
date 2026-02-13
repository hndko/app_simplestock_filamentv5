<?php

namespace App\Filament\Resources\Suppliers;

use App\Filament\Resources\Suppliers\Pages\CreateSupplier;
use App\Filament\Resources\Suppliers\Pages\EditSupplier;
use App\Filament\Resources\Suppliers\Pages\ListSuppliers;
use App\Filament\Resources\Suppliers\Schemas\SupplierForm;
use App\Filament\Resources\Suppliers\Tables\SuppliersTable;
use App\Models\Supplier;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    // ubah icon menu sidebar (pilih salah satu icon dari https://heroicons.com/)
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    // ubah label menu sidebar
    protected static ?string $navigationLabel = 'Suppliers';

    // grup menu (opsional)
    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $recordTitleAttribute = 'Supplier';

    public static function form(Schema $schema): Schema
    {
        // return SupplierForm::configure($schema);
        return $schema
            ->schema([
                // Membuat Section agar tampilan form lebih rapi (ada kotak putihnya)
                Section::make('Informasi Supplier')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Perusahaan')
                            ->required() // Wajib diisi
                            ->maxLength(255),

                        TextInput::make('contact_person')
                            ->label('Nama Kontak (Sales)')
                            ->maxLength(255),

                        TextInput::make('email')
                            ->email() // Validasi format email
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->maxLength(255),

                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->columnSpanFull(), // Agar lebar memenuhi baris
                    ])->columns(2), // Form dibagi 2 kolom
            ]);
    }

    public static function table(Table $table): Table
    {
        // return SuppliersTable::configure($table);

        return $table
            ->columns([
                TextColumn::make('row_number')
                    ->label('#')
                    ->rowIndex(), // Bisa diurutkan

                TextColumn::make('name')
                    ->label('Perusahaan')
                    ->searchable() // Bisa dicari
                    ->sortable() // Bisa diurutkan
                    ->weight('bold'), // Huruf tebal

                TextColumn::make('contact_person')
                    ->label('Kontak')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->icon('heroicon-m-phone'),

                TextColumn::make('email')
                    ->icon('heroicon-m-envelope')
                    ->toggleable(isToggledHiddenByDefault: true), // Default sembunyi, bisa dimunculkan user
            ])
            ->filters([
                // Nanti kita isi filter jika perlu
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuppliers::route('/'),
            'create' => CreateSupplier::route('/create'),
            'edit' => EditSupplier::route('/{record}/edit'),
        ];
    }
}
