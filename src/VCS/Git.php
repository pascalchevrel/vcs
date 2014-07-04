<?php
namespace VCS;

class Git extends Base
{
    public function getCommits()
    {
        $log = $this->execute(
            "git log --no-merges --format='changeset: %H%nuser: %aN <%aE>%ndate: %ad%nsummary: %s%n'"
        );

        return $this->parseLog($log);
    }
}

