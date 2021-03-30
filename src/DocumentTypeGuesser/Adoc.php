<?php
namespace Dokobit\DocumentTypeGuesser;

class Adoc implements DocumentTypeGuesserInterface
{
    public function guess($content, $extension)
    {
        if ($extension != 'adoc') {
            return;
        }

        return 'adoc';
    }
}
