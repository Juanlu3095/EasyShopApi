<?php

namespace App\Http\Controllers;

use App\Http\Requests\BusquedaproductoRequest;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Productcategory;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return ProductResource::collection($products);
    }
    
    /**
     * Display a listing of published products.
     */
    public function indexPublished()
    {
        $products = Product::where('estado_producto', 'publicada')->orderBy('updated_at', 'desc')->get();
        return ProductResource::collection($products);
    }

    /**
     * Display a listing of the last five published products ordered by updated_at.
     */
    public function indexNovedades()
    {
        $novedades = Product::where('estado_producto', 'publicada')->orderBy('updated_at', 'desc')->limit(5)->get();
        return ProductResource::collection($novedades);
    }

    /**
     * Display a listing of products by category.
     */
    public function indexPorCategoria(string $slug)
    {
        $category = Productcategory::where('slug', $slug)->first();
        $products = Product::where('productcategory_id', $category->id)->where('estado_producto', 'publicada')->get();
        return ProductResource::collection($products);
    }

    /**
     * Display a listing of products by brand.
     */
    public function indexPorMarca(string $id)
    {
        $products = Product::where('brand_id', $id)->where('estado_producto', 'publicada')->get();
        return ProductResource::collection(($products));
    }

    /**
     * Display a listing of related products by category, excluding principal product.
     */
    public function productosRelacionados(Request $request)
    {
        $products = Product::where('productcategory_id', $request->categoria_id)
        ->where('id', '!=', 1)
        ->where('estado_producto', 'publicada')
        ->limit(3)->get();
        return ProductResource::collection(($products));
    }

    /**
     * Display a listing of products using a filter by prize, category and brand.
     */
    public function filtrarProductos(Request $request)
    {
        $filtroCategoria = $request->categoria;
        $filtroMarca = $request->marca;
        $filtroPreciomin = $request->preciomin;
        $filtroPreciomax = $request->preciomax;

        $products = Product::when($filtroCategoria, function ($query, int $filtroCategoria) {
            $query->where('productcategory_id', $filtroCategoria)->orderBy('id', 'desc');
            })
            ->when($filtroMarca, function ($query, int $filtroMarca) {
                $query->where('brand_id', $filtroMarca)->orderBy('id', 'desc');
            })
            ->when($filtroPreciomin, function ($query, int $filtroPreciomin) {
                $query->where('precio', '>', $filtroPreciomin)->orderBy('id', 'desc');
            })
            ->when($filtroPreciomax, function ($query, int $filtroPreciomax) {
                $query->where('precio', '<', $filtroPreciomax)->orderBy('id', 'desc');
            })
            ->where('estado_producto', 'publicada')
            ->get();
        
            if($products) {
                return response()->json([
                    'success' => true,
                    'result' => ProductResource::collection($products)
                ], 200);
    
            } else {
                return response()->json([
                    'success' => true,
                    'result' => 'No hay productos para mostrar'
                ], 404); 
            }
    }

    /**
     * Display a listing of products using a filter by search.
     */
    public function buscarProductos(BusquedaproductoRequest $request)
    {
        $busqueda = $request->query('busqueda');

        $products = Product::where('nombre', 'like', '%' . $busqueda . '%')->orderBy('id', 'desc')->get();

        if($products) {

            return response()->json([
                'success' => true,
                'result' => ProductResource::collection($products)
            ], 200);

        } else {

            return response()->json([
                    'success' => false,
                    'result' => 'No hay productos para mostrar'
            ], 404); 
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $category = Productcategory::find($request->categoria_id); // Buscamos la categoría
        $brand = Brand::find($request->marca_id); // Buscamos la marca

        // Verificamos que la categoría exista, y si es así, se crea el producto
        if($category && $brand) {
            $product = $category->products()->create([ // No es necesario añadir la id de la categoría en el update si está en $category ya que products()
                'nombre' => $request->nombre,          // ya asegura la relación entre ambas tablas
                'descripcion' => $request->descripcion,
                'descripcion_corta' => $request->descripcion_corta,
                'productcategory_id' => $request->categoria_id,
                'brand_id' => $request->marca_id,
                'estado_producto' => $request->estado,
                'precio' => $request->precio,
                'precio_rebajado' => $request->precio_rebajado,
                'sku' => $request->sku,
                'isbn_ean' => $request->isbn_ean,
                'inventario' => $request->inventario
            ]);

            $productId = $product->id;
            $imageId = Image::find($request->imagen_id);

            if($imageId) {
                $imageId->update([
                    'imageable_id' => $productId,
                    'imageable_type' => Product::class
                ]);
            }
            
            return response()->json([
                'result' => 'Producto creado.',
                'data' => $product->id
            ], 201);

        } else {
            return response()->json([
                'result' => 'La categoría y/o la marca no existen.'
            ], 404);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);

        if($product) {
            return new ProductResource($product);

        } else {
            return response()->json([
                'result' => 'No se ha encontrado el producto.'
            ], 404);
        }
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $product_id)
    {
        $category = Productcategory::find($request->categoria_id); // Buscamos la categoría
        $brand = Brand::find($request->marca_id); // Buscamos la marca
        $product = Product::find($product_id);

        if($brand && $category) {

            $product->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'descripcion_corta' => $request->descripcion_corta,
                'productcategory_id' => $request->categoria_id,
                'brand_id' => $request->marca_id,
                'estado_producto' => $request->estado,
                'precio' => $request->precio,
                'precio_rebajado' => $request->precio_rebajado,
                'sku' => $request->sku,
                'isbn_ean' => $request->isbn_ean,
                'inventario' => $request->inventario
            ]);

            // Vinculamos la nueva imagen seleccionada si ya estaba en la base de datos
            $nuevaImagen = Image::find($request->imagen_id); // Si se usase $brand->images()->update([...]), se actualizarán todas las images vinculadas

            if($nuevaImagen) { // SI EXISTE LA IMAGEN, ACTUALIZAR LA ID Y EL TYPE

                if(!$nuevaImagen->imageable_id || $nuevaImagen->imageable_id == $product_id && $nuevaImagen->imageable_type == Product::class) {

                    // Desvinculamos la imagen actual del producto si la hay.
                    // Ésto sólo lo hacemos en caso de que la id de la categoria no coincida con imageable_id de la imagen, para que no se pierda la imagen asignada, para ello el if
                    if($nuevaImagen->imageable_id != $product_id) {
                        $product->images()->update([
                            'imageable_id' => null,
                            'imageable_type' => null
                        ]);
                    }
                    
                    $nuevaImagen->update([
                        'imageable_id' => $product->id,
                        'imageable_type' => Product::class, // se añade App\Models\Product
                    ]);

                    return response()->json([
                        'result' => 'Marca modificada.',
                        'data' => $product
                    ], 200);

                } else {
                    return response()->json([
                        'result' => 'La imagen ya está asignada a otro elemento.',    
                    ], 403);
                }
                
            } else {
                return response()->json([
                    'result' => 'Imagen no encontrada.'
                ], 404);
            }


        } else {
            return response()->json([
                'result' => 'La categoría y/o la marca no existen.'
            ], 404);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $idArray)
    {
        $ids = explode(",",$idArray); // Obtenemos las id del Array
        $products = Product::find($ids); // Buscamos los productos en la base de datos con las id

        if($products) {
            $products->map(function($product) { // Mapeamos los productos obtenidas de la BD para quitar todas las imágenes asignadas a cada id
                $product->images()->update([ // Para cada producto desvinculamos las imágenes asignadas antes de eliminar los productos
                    'imageable_id' => null,
                    'imageable_type' => null
                ]);
    
                $product->delete(); // Eliminamos el producto

            });

            return response()->json([
                'result' => 'Producto eliminado.'
            ], 200);

        } else {
            return response()->json([
                'result' => 'No se ha podido procesar la petición.'
            ], 404);
        }
        
        
    }
}
