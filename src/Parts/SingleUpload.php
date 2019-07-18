<?php
/* Digraph Core | https://gitlab.com/byjoby/digraph-core | MIT License */
namespace Digraph\Modules\Submissions\Parts;

class SingleUpload extends AbstractPartsClass
{
    public function construct()
    {
        //set up a single upload chunk
        $this->chunks['upload'] = new Chunks\Upload($this, 'upload', 'Uploaded file');
    }
}
