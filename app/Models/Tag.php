<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function tagExists($data){
        $tagArr = [];
        foreach($data as $tag){
            $tagrow = Tag::where('id',$tag)->orWhere('name',$tag)->first();
            if(!$tagrow){
                if (($key = array_search($tag, $data)) !== false) {
                    unset($data[$key]);
                    $created = new Tag();
                        $created->name = $tag;
                    $created->save();
                    if($created){
                        $tagArr[]=$created->id;   
                    }
                }
            }
        }
        return implode(" ",array_values(array_merge($tagArr,$data)));
    }
}
