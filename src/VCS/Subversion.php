<?php
namespace VCS;

use DateTime;

class Subversion extends Base
{
    public function getCommits()
    {
        $log = $this->execute(
            'svn log'
        );

        return $this->parseLog($log);
    }

    protected function parseLog($log)
    {
        $commits = [];

        for ($i = 0, $lines = count($log); $i < $lines; $i++) {
            if (!strncmp($log[$i], '----', strlen('----'))) {
                continue;
            }

            if (!strncmp($log[$i], 'r', strlen('r'))) {
                $tmp  = explode('|', $log[$i]);
                $tmp  = array_map('trim', $tmp);
                $date = trim(preg_replace('~\(([:alpha]*.+)\)~i', '', $tmp[2]));
                $commits[] = [
                    'commit'  => ltrim ($tmp[0],'r'),
                    'author'  => $tmp[1],
                    'email'   => $tmp[1],
                    'date'    => DateTime::createFromFormat('Y-m-d H:i:s O', $date),
                    'summary' => $log[$i+2]
                ];
                //Jump to next commit
                $i = $i + 2;
            }
        }

        return $commits;
    }
}
