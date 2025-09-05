<?php

namespace App\Domain\Skills;

use App\Models\Skill;
use RuntimeException;

class PrerequisiteValidator
{
    /** Detect circular dependencies using DFS */
    public function assertNoCycles(): void
    {
        $graph = [];
        Skill::with('prerequisites:id')->get(['id'])->each(function(Skill $s) use (&$graph) {
            $graph[$s->id] = $s->prerequisites->pluck('id')->all();
        });

        $visited = $stack = [];
        $visit = function($node) use (&$graph, &$visited, &$stack, &$visit) {
            if (($stack[$node] ?? false) === true) throw new RuntimeException("Circular prerequisite at skill {$node}");
            if ($visited[$node] ?? false) return;
            $visited[$node] = true; $stack[$node] = true;
            foreach ($graph[$node] ?? [] as $n) $visit($n);
            $stack[$node] = false;
        };

        foreach (array_keys($graph) as $id) $visit($id);
    }
}
