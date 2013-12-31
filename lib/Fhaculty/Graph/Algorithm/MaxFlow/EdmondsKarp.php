<?php

namespace Fhaculty\Graph\Algorithm\MaxFlow;

use Fhaculty\Graph\Exception\OutOfBoundsException;

use Fhaculty\Graph\Algorithm\ShortestPath\BreadthFirst;

use Fhaculty\Graph\Exception\InvalidArgumentException;

use Fhaculty\Graph\Exception\UnexpectedValueException;

use Fhaculty\Graph\Edge\Directed as EdgeDirected;

use Fhaculty\Graph\Exception\UnderflowException;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Edge\Base as Edge;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Algorithm\ResidualGraph;
use Fhaculty\Graph\Exception;

class EdmondsKarp implements Base
{
    /**
     *
     * @param Vertex $startVertex       the vertex where the flow search starts
     * @param Vertex $destinationVertex the vertex where the flow search ends (destination)
     */
    public function createResult(Vertex $startVertex, Vertex $destinationVertex)
    {
        if ($startVertex === $destinationVertex) {
            throw new InvalidArgumentException('Start and destination must not be the same vertex');
        }
        if ($startVertex->getGraph() !== $destinationVertex->getGraph()) {
            throw new InvalidArgumentException('Start and target vertex have to be in the same graph instance');
        }

        $maxFlowGraph = $this->createGraph($startVertex, $destinationVertex);

        return new ResultFromMaxFlowGraph($startVertex, $maxFlowGraph);
    }

    /**
     * Returns max flow graph
     *
     * @param Vertex $startVertex       the vertex where the flow search starts
     * @param Vertex $destinationVertex the vertex where the flow search ends (destination)
     * @return Graph
     */
    private function createGraph(Vertex $startVertex, Vertex $destinationVertex)
    {
        $graphResult = $startVertex->getGraph()->createGraphClone();

        // initialize null flow and check edges
        foreach ($graphResult->getEdges() as $edge) {
            if (!($edge instanceof EdgeDirected)) {
                throw new UnexpectedValueException('Undirected edges not supported for edmonds karp');
            }
            $edge->setFlow(0);
        }

        $idA = $startVertex->getId();
        $idB = $destinationVertex->getId();

        do {
            // Generate new residual graph and repeat
            $residualAlgorithm = new ResidualGraph($graphResult);
            $graphResidual = $residualAlgorithm->createGraph();

            // 1. Search _shortest_ (number of hops and cheapest) path from s -> t
            $alg = new BreadthFirst($graphResidual->getVertex($idA));
            try {
                $pathFlow = $alg->getWalkTo($graphResidual->getVertex($idB));
            } catch (OutOfBoundsException $e) {
                $pathFlow = NULL;
            }

            // If path exists add the new flow to graph
            if ($pathFlow) {
                // 2. get max flow from path
                $maxFlowValue = $pathFlow->getEdges()->getEdgeOrder(Edges::ORDER_CAPACITY)->getCapacity();

                // 3. add flow to path
                foreach ($pathFlow->getEdges() as $edge) {
                    // try to look for forward edge to increase flow
                    try {
                        $originalEdge = $graphResult->getEdgeClone($edge);
                        $originalEdge->setFlow($originalEdge->getFlow() + $maxFlowValue);
                    // forward edge not found, look for back edge to decrease flow
                    } catch (UnderflowException $e) {
                        $originalEdge = $graphResult->getEdgeCloneInverted($edge);
                        $originalEdge->setFlow($originalEdge->getFlow() - $maxFlowValue);
                    }
                }
            }

        // repeat while we still finds paths with residual capacity to add flow to
        } while ($pathFlow);

        return $graphResult;
    }
}
