<?php
//most latest change
namespace App\Http\Controllers;

use App\DataTables\ProductsDataTable;
use App\Models\Product;
use App\Models\Profile;
use App\Models\Utils;
use App\Models\Attribute;
use App\Models\Chat;
use App\Models\Image;
use App\Models\MenuItem;
use Hamcrest\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class DashboardProductsControler extends Controller
{
    public function index(ProductsDataTable $dataTable)
    {
        return $dataTable->render('metro.dashboard.products');
    }

    public function create()
    {
        return view('metro.dashboard.product-create');
    }

    public function show()
    {
        return view('metro.dashboard.chats');
    }

    public function store(Request $r)
    {


        if (isset($_POST['task'])) {
            $task = trim($_POST['task']);
        }



        if ($task == 'create') {
            $pro = new Product();
            $u = Auth::user();

            $pro->name = $r->name;
            $pro->category_id = $r->category_id;
            $pro->sub_category_id = $r->category_id;
            $pro->nature_of_offer = $r->nature_of_offer;
            $pro->price = $r->price;
            $pro->fixed_price = $r->fixed_price;
            $pro->quantity = $r->quantity;
            $pro->city_id = $r->city_id;
            $pro->description = trim($r->description);
            $pro->user_id = $u->id;
            $pro->attributes = '[]';
            $task = "";
            $images = Image::where(['name' => 'temp', 'user_id' => $u->id])->get();
            $pro->thumbnail = '';
            $pro->images = '[]';

            if (isset($_FILES['avatar'])) {

                $img = $_FILES['avatar'];
                $raw_images = [];
                $raw_images['name'][] = $img['name'];
                $raw_images['type'][] = 'image/png';
                $raw_images['tmp_name'][] = $img['tmp_name'];
                $raw_images['error'][] = $img['error'];
                $raw_images['size'][] = $img['size'];

                $temp_img = Utils::upload_images($raw_images);
                if ($temp_img != null) {
                    if (isset($temp_img[0])) {
                        $pro->thumbnail = json_encode($temp_img[0]);
                    }
                }
            }

            if (isset($images[0])) {
                if (strlen($pro->thumbnail) < 3) {
                    $pro->thumbnail = json_encode($images[0]);
                }
                $pro->images = json_encode($images);
                foreach ($images as $key => $im) {
                    $im->delete();
                }
            }

            if (strlen($pro->thumbnail) < 3) {
                die("You must upload atleast one image.");
            }

            $pro->save();
            return redirect('dashboard/products');
            die();
        }


        die("posting...");

        die("time to stire shit");
        if (isset($_POST['delete'])) {
            $id = (int)($r->delete);
            $item = MenuItem::find($id);
            if ($item != null) {
                $item->delete();
                return true;
            }
            return 0;
        }

        if (isset($_POST['edit'])) {
            $id = (int)($r->edit);
            if ($id == 0) {
                die("new");
            } else {
                $item = MenuItem::find($id);
                if ($item == null) {
                    dd("Item not found.");
                }
                $item->parent_id = $r->parent_id;
                $item->title = $r->title;
                $item->uri = $r->uri;
                $item->icon = $r->icon;
                $item->save();
            }

            return redirect('dashboard/menu');
        } else if (isset($_POST['create'])) {

            $item = new MenuItem();
            $item->parent_id = $r->parent_id;
            $item->title = $r->title;
            $item->uri = $r->uri;
            $item->icon = $r->icon;
            $item->save();
            return redirect('dashboard/menu');
        }


        if (isset($_POST['_order'])) {
            $items = json_decode($r->_order);
            foreach ($items as $parent_key => $parent) {
                $p = MenuItem::find($parent->id);
                $order = 0;

                if ($p != null) {
                    $p->order = $order;
                    $p->parent_id = 0;
                    $p->save();
                    $order++;

                    if (isset($parent->children) && ($parent->children != null)  && (!empty($parent->children))) {
                        foreach ($parent->children as $kid_key => $kid) {
                            $k = MenuItem::find($kid->id);
                            if ($k != null) {
                                $order++;
                                $k->order = $order;
                                $k->parent_id = $p->id;
                                $k->save();
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
}
