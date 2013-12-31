<?php

namespace Fhaculty\Graph\Algorithm\MaxFlow;

use Fhaculty\Graph\Vertex;

interface Base
{
    /**
     * @param Vertex $startVertex       the vertex where the flow search starts
     * @param Vertex $destinationVertex the vertex where the flow search ends (destination)
     * @return Result
     */
    public function createResult(Vertex $startVertex, Vertex $destinationVertex);
}
