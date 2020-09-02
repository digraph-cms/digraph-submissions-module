<?php
/* Digraph Core | https://gitlab.com/byjoby/digraph-core | MIT License */
namespace Digraph\Modules\Submissions\Parts;

use Digraph\Modules\Submissions\Submission;

abstract class AbstractPartsClass
{
    protected $submission;
    protected $chunks = [];

    abstract public function construct();

    public function chunks()
    {
        return $this->chunks;
    }

    public function complete()
    {
        foreach ($this->chunks as $chunk) {
            if (!$chunk->complete()) {
                return false;
            }
        }
        return true;
    }

    public function submission()
    {
        return $this->submission;
    }

    public function __construct(Submission $submission)
    {
        $this->submission = $submission;
        $this->chunks['submission'] = new Chunks\SubmissionMeta($this, 'submission', 'Submission information');
        $this->chunks['submitter'] = new Chunks\SubmitterMeta($this, 'submitter', 'Submitter information');
        $this->construct();
    }
}
