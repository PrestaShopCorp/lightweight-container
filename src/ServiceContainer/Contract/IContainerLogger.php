<?php

namespace PrestaShopCorp\LightweightContainer\ServiceContainer\Contract;

interface IContainerLogger
{
    /**
     * @param string $message
     *
     * @return void
     */
    public function debug($message);

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function info($message);

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function warn($message);

    /**
     * @param string $message
     *
     * @return mixed
     */
    public function error($message);
}