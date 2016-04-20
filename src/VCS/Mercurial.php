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
        $log = $this->execute('hg log --config ui.verbose=false');
        $this->repository_type = 'hg';

        return $this->parseLog($log);
    }

    /**
     * Get the list of Mercurial commits for the repository as a structured array
     * since a specific revision
     *
     * @return array List of commits
     */
    public function getCommitsSince($rev)
    {
        $log = $this->execute("hg log -r tip:{$rev} --config ui.verbose=false");
        $this->repository_type = 'hg';

        return $this->parseLog($log);
    }

    /**
     * Get the list of files changed with the type of change for a given revision.
     * Results are returned as a structured array.
     *
     * @param String $rev A valid changeset for this repo
     * @return array List of file changes
     */
    public function getChangedFiles($rev)
    {
        $raw_changes = $this->execute('hg status --change ' . $rev);
        $this->repository_type = 'hg';

        $changes = [];
        foreach ($raw_changes as $key => $change) {
            $exploded_change = explode(' ', $change);
            $changes[$key]['type'] = $exploded_change[0];
            $changes[$key]['path'] = $exploded_change[1];
        }

        return $changes;
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

    /**
     * Revert a file to a specific revision.
     *
     * @param String $rev A valid changeset for this repo
     * @param String $path The path of a file
     * @return void
     */
    public function revertFile($rev, $path) {
        $this->execute("hg revert -r {$rev} {$path}");
    }

    /**
     * Revert a repo to a specific revision.
     *
     * @param String $rev A valid changeset for this repo
     * @return void
     */
    public function revertRepo($rev) {
        $this->execute("hg update -r {$rev}");
    }

    /**
     * Get the latest changeset hash of the repo
     * @return String Hash of the current changeset
     */
    public function getLatestChangeset() {
        return $this->execute('hg id -i')[0];
    }
}
