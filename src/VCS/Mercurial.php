<?php
namespace VCS;

class Mercurial extends Base
{
    /**
     * Get the list of Mercurial commits for the repository as a structured array
     *
     * @return array List of commits
     */
    public function getCommits()
    {
        $log = $this->execute('hg log');
        $this->repository_type = 'hg';

        return $this->parseLog($log);
    }

    /**
     * Do a "hg pull -r default ; hg update -C" command
     * @return void
     */
    public function update()
    {
        $this->execute('hg pull -r default');
        $this->execute('hg update -C');
    }
}
