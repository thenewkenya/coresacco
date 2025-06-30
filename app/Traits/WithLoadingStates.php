<?php

namespace App\Traits;

trait WithLoadingStates
{
    public array $isLoading = [];

    public function startLoading(string $action): void
    {
        $this->isLoading[$action] = true;
    }

    public function stopLoading(string $action): void
    {
        $this->isLoading[$action] = false;
    }

    public function isLoading(string $action): bool
    {
        return $this->isLoading[$action] ?? false;
    }

    protected function handleLoadingState(string $action, callable $callback)
    {
        try {
            $this->startLoading($action);
            $result = $callback();
            $this->stopLoading($action);
            return $result;
        } catch (\Exception $e) {
            $this->stopLoading($action);
            throw $e;
        }
    }
} 