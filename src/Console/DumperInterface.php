<?php

namespace TravisSouth\Gitup\Console;

interface DumperInterface
{
    /**
     * Dumps anything on info level
     *
     * @param mixed $anything
     *
     * @return void
     */
    public function info($anything);

    /**
     * Dumps anything on error level
     *
     * @param mixed $anything
     *
     * @return void
     */
    public function error($anything);

    /**
     * Dumps anything on debug level
     *
     * @param mixed $anything
     *
     * @return void
     */
    public function debug($anything);

    /**
     * Dumps anything on notice level
     *
     * @param mixed $anything
     *
     * @return void
     */
    public function notice($anything);
}
