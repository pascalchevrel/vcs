<?php
namespace VCS;

class Mercurial extends Base
{
    public function getCommits()
    {
        $log = $this->execute('hg log');

        return $this->parseLog($log);
    }
}
