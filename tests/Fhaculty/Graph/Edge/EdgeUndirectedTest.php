<?php

class EdgeUndirectedTest extends EdgeBaseTest
{
    protected function createEdge()
    {
        // 1 -- 2
        return $this->v1->createEdge($this->v2);
    }

    public function testVerticesEnds()
    {
        $this->assertEquals(array($this->v1, $this->v2), $this->edge->getVerticesStart()->getVector());
        $this->assertEquals(array($this->v2, $this->v1), $this->edge->getVerticesTarget()->getVector());
    }

    public function testConnection()
    {
        $this->assertTrue($this->edge->isConnection($this->v1, $this->v2));
        $this->assertTrue($this->edge->isConnection($this->v2, $this->v1));

        $this->assertFalse($this->edge->isConnection($this->v1, $this->v1));
        $this->assertFalse($this->edge->isConnection($this->v2, $this->v2));
    }
}
