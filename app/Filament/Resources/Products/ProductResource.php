<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Resources\Products\Schemas\ProductForm;
use App\Filament\Resources\Products\Tables\ProductsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $recordTitleAttribute = 'Product';

    public static function form(Schema $schema): Schema
    {
        // return ProductForm::configure($schema);
        return $schema
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('Informasi Produk')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true) // Aktif saat cursor pindah
                                    // Otomatis isi slug saat nama diketik
                                    ->afterStateUpdated(
                                        fn(string $operation, $state, Set $set) =>
                                        $operation === 'create' ? $set('slug', Str::slug($state)) : null
                                    ),

                                TextInput::make('slug')
                                    ->required()
                                    ->disabled() // Tidak bisa diedit manual
                                    ->dehydrated() // Tetap dikirim ke database meski disabled
                                    ->unique(Product::class, 'slug', ignoreRecord: true),

                                RichEditor::make('description')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Section::make('Gambar')
                            ->schema([
                                FileUpload::make('image')
                                    ->image() // Hanya boleh gambar
                                    ->directory('products') // Simpan di folder storage/app/public/products
                            ])
                    ])->columnSpan(2), // Ambil 2/3 lebar layar

                Group::make()
                    ->schema([
                        Section::make('Harga & Stok')
                            ->schema([
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->prefix('Rp'),

                                TextInput::make('stock')
                                    ->required()
                                    ->numeric()
                                    ->default(0),

                                // PENTING: Select Relasi ke Supplier
                                Select::make('supplier_id')
                                    ->relationship('supplier', 'name') // Cari nama supplier
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([ // Fitur Quick Create Supplier baru langsung dari sini!
                                        TextInput::make('name')->required(),
                                        TextInput::make('email')->email(),
                                    ]),
                            ]),

                        Section::make('Status')
                            ->schema([
                                Toggle::make('is_active')
                                    ->label('Aktif?')
                                    ->default(true),
                            ])
                    ])->columnSpan(1), // Ambil 1/3 lebar layar
            ])->columns(3); // Total Grid ada 3 kolom
    }

    public static function table(Table $table): Table
    {
        // return ProductsTable::configure($table);

        return $table
            ->columns([
                TextColumn::make('row_number')
                    ->label('#')
                    ->sortable()
                    ->rowIndex(),

                ImageColumn::make('image')
                    ->circular(), // Gambar bulat

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('supplier.name') // Menampilkan nama supplier via relasi
                    ->label('Supplier')
                    ->sortable(),

                TextColumn::make('price')
                    ->money('IDR') // Format otomatis Rupiah
                    ->sortable(),

                TextColumn::make('stock')
                    ->numeric()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('supplier')
                    ->relationship('supplier', 'name') // Filter berdasarkan Supplier
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
