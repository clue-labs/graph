<?php

namespace Fhaculty\Graph\Algorithm\MaxFlow;

interface Result
{
    /**
     * create a max flow graph
     *
     * @return Graph
     */
    public function createGraph();

    /**
     * Returns max flow value
     *
     * @return double
     */
    public function getFlowMax();
}
