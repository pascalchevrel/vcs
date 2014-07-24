<?php
namespace VCS;

class Git extends Base
{
    /**
     * Get the list of Git commits for the repository as a structured array
     *
     * @return array List of commits
     */
    public function getCommits()
    {
        $log = $this->execute(
            "git log --no-merges --format='changeset: %H%nuser: %aN <%aE>%ndate: %ad%nsummary: %s%n'"
        );
        $this->$repository_type = 'git';

        return $this->parseLog($log);
    }

    /**
     * Do a "git fetch --all ; git pull --all" command
     * @return void
     */
    public function pull()
    {
        $this->execute('git fetch --all');
        $this->execute('git pull --all');
    }
}
