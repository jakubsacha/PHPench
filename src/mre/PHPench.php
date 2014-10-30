<?php

namespace mre;

use Gregwar\GnuPlot\GnuPlot;
use PHP_Timer;

/**
 * PHPench
 *
 * This class provides the core functionality for the PHPench package.
 *
 * @link http://github.com/mre/PHPench
 *
 * @author Matthias Endler <matthias-endler@gmx.net>
 * @author Markus Poerschke <markus@eluceo.de>
 */
class PHPench
{
    private $tests = [];
    private $titles = [];

    public function __construct($title = 'untitled')
    {
        $this->plot = new GnuPlot();
        $this->plot->reset();
        $this->plot->setGraphTitle($title);
    }

    /**
     * Adds an test to the bench instance
     *
     * @param callable $test
     */
    public function addTest(\Closure $test, $title)
    {
        $this->tests[] = $test;
        $this->titles[] = $title;
    }

    /**
     * Plots the graph for all added tests
     *
     * @param array    $benchRange
     * @param bool     $keepAlive
     */
    public function plot(array $benchRange, $keepAlive = false)
    {
        // set titles
        foreach ($this->titles as $index => $title) {
            $this->plot->setTitle($index, $title);
        }

        foreach ($benchRange as $i) {
            foreach ($this->tests as $index => $test) {
                $this->bench($test, $i, $index);
            }

            $this->plot->refresh();
        }

        if ($keepAlive) {
            // Wait for user input to close
            echo "Press enter to quit.";
            fgets(STDIN);
        }
    }

    /**
     * This method will save the graph as a PNG image
     *
     * @param string $filename
     * @param int    $width
     * @param int    $height
     */
    public function save($filename, $width = 400, $height = 300)
    {
        $this->plot->setWidth($width)
                   ->setHeight($height)
                   ->writePng($filename);
    }

    private function bench($benchFunction, $i, $plotIndex)
    {
        PHP_Timer::start();
        $benchFunction($i);
        $time = PHP_Timer::stop();
        $this->plot->push($i, $time, $plotIndex);
    }
}
