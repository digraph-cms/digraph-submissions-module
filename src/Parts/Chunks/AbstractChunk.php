<?php
/* Digraph Core | https://gitlab.com/byjoby/digraph-core | MIT License */
namespace Digraph\Modules\Submissions\Parts\Chunks;

use Digraph\Modules\Submissions\Parts\AbstractPartsClass;
use Formward\Form;

abstract class AbstractChunk
{
    protected $parts;
    protected $name;
    protected $label;
    protected $optOutMessage = null;
    protected $instructions = null;
    protected $form = null;

    abstract public function body_complete();
    abstract public function body_form() : Form;
    abstract public function form_handle(Form &$form);

    public function instructions($set=null)
    {
        if ($set !== null) {
            $this->instructions = $set;
        }
        return $this->instructions;
    }

    public function optOut($set=null)
    {
        $s = $this->submission();
        if ($set !== null) {
            $s['submission_optouts.'.$this->name] = $set;
            $s->update();
        }
        return $s['submission_optouts.'.$this->name];
    }

    public function optional($set=null)
    {
        if ($set !== null) {
            $this->optOutMessage = $set;
        }
        return !!$this->optOutMessage;
    }

    protected function &form()
    {
        if (!$this->form) {
            $this->form = new Form('', md5(serialize([$this->name,$this->label])));
        }
        return $this->form;
    }

    public function complete()
    {
        return $this->optOut() || isset($this->submission()[$this->name]);
    }

    public function body_edit()
    {
        $form = $this->body_form();
        if ($this->complete()) {
            $form->submitButton()->label('Save changes');
        } else {
            $form->submitButton()->label('Save section');
        }
        if ($form->handle()) {
            $this->form_handle($form);
            $this->optOut(false);
            $url = $this->submission()->url('chunk', [
                'chunk' => $this->name
            ], true);
            header('Location: '.$url);
            exit();
        }
        echo $form;
    }

    public function body_incomplete()
    {
        echo "<em>This section is incomplete</em>";
    }

    public function submission()
    {
        return $this->parts->submission();
    }

    public function body()
    {
        ob_start();
        $mode = ($this->complete()?'complete':'incomplete');
        if ($mode == 'incomplete') {
            if ($this->submission()->isEditable()) {
                $mode .= ' editing';
            }
        } elseif (@$_GET['edit']) {
            $mode .= ' editing';
        }
        //open chunk and add label
        echo "<div class='submission-chunk $mode'>";
        echo "<div class='submission-chunk-label'>".$this->label."</div>";
        //main body
        if ($this->submission()->isEditable() && @$_GET['edit']) {
            //add opt-out interface
            if ($this->optOutMessage) {
                echo "<div class='digraph-block opt-out'>";
                $url = $this->submission()->url('chunk', [
                    'chunk' => $this->name,
                    'optout' => true
                ], true);
                echo "<a class='cta-button green' style='font-size:1em;' href='$url'>".$this->optOutMessage."</a>";
                echo "</div>";
            }
            //instructions
            if ($this->instructions) {
                echo "<div class='digraph-block instructions'>".$this->instructions."</div>";
            }
            //display editing form if editing is allowed and either incomplete or edit requested
            echo $this->body_edit();
        } elseif ($this->complete()) {
            //display complete content if completed
            if ($this->optOut()) {
                echo $this->optOutMessage;
            } else {
                echo $this->body_complete();
            }
        } else {
            //display incomplete content if incomplete and not editable
            echo $this->body_incomplete();
        }
        //display edit/cancel edit links
        if ($this->submission()->isEditable()) {
            if (@$_GET['edit']) {
                $url = $this->submission()->url('chunk', [
                    'chunk' => $this->name
                ], true);
                echo "<a class='mode-switch' href='$url'>Cancel editing</a>";
            } else {
                $url = $this->submission()->url('chunk', [
                    'chunk' => $this->name,
                    'edit' => true
                ], true);
                if ($this->complete()) {
                    echo "<a class='mode-switch' href='$url'>Edit this section</a>";
                } else {
                    echo "<a class='mode-switch' href='$url'>Complete this section</a>";
                }
            }
        }
        echo "</div>";
        //if form was used, remove opt-out
        if ($this->form && $this->form->handle()) {
            $this->optOut(false);
        }
        //output
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }

    public function __construct(AbstractPartsClass &$parts, string $name, string $label)
    {
        $this->parts = $parts;
        $this->name = $name;
        $this->label = $label;
    }
}
