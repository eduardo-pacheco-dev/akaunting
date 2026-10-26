<?php

namespace App\Jobs\Document;

use App\Abstracts\Job;
use App\Events\Document\DocumentCreated;
use App\Events\Document\DocumentCreating;
use App\Interfaces\Job\HasOwner;
use App\Interfaces\Job\HasSource;
use App\Interfaces\Job\ShouldCreate;
use App\Jobs\Document\CreateDocumentItemsAndTotals;
use App\Models\Document\Document;
use Illuminate\Support\Str;

class CreateDocument extends Job implements HasOwner, HasSource, ShouldCreate
{
    public function handle(): Document
    {
        // Derived from the lines by CreateDocumentItemsAndTotals, never taken from the caller
        $this->request['amount'] = 0;

        // Disable this lines for global discount issue fixed ( https://github.com/akaunting/akaunting/issues/2797 )
        if (! empty($this->request['discount'])) {
            $this->request['discount_rate'] = $this->request['discount'];
        }

        event(new DocumentCreating($this->request));

        \DB::transaction(function () {
            $this->model = Document::create($this->request->all());

            // Upload attachment
            if ($this->request->file('attachment')) {
                foreach ($this->request->file('attachment') as $attachment) {
                    $media = $this->getMedia($attachment, Str::plural($this->model->type));

                    $this->model->attachMedia($media, 'attachment');
                }
            }

            $this->dispatch(new CreateDocumentItemsAndTotals($this->model, $this->request));

            $this->model->update($this->request->all());

            $this->model->createRecurring($this->request->all());
        });

        event(new DocumentCreated($this->model, $this->request));

        return $this->model;
    }
}
