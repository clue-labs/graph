<?php

namespace Fhaculty\Graph\Algorithm\MaxFlow;

use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Graph;

class ResultFromMaxFlowGraph implements Result
{
    private $startVertex;
    private $maxFlowGraph;

    public function __construct(Vertex $startVertex, Graph $maxFlowGraph)
    {
        $this->startVertex = $startVertex;
        $this->maxFlowGraph = $maxFlowGraph;
    }

    public function createGraph()
    {
        return $this->maxFlowGraph->createGraphClone();
    }

    public function getFlowMax()
    {
        $start = $this->maxFlowGraph->getVertex($this->startVertex->getId());
        $maxFlow = 0;
        foreach ($start->getEdgesOut() as $edge) {
            $maxFlow = $maxFlow + $edge->getFlow();
        }

        return $maxFlow;
    }
}
