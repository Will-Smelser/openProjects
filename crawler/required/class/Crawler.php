<?php
/**
 * Author: Will Smelser
 * Date: 1/10/14
 * Time: 9:00 PM
 * Project: openProjects
 */
require_once 'PageLoad.php';
require_once 'Utils.php';

class Crawler {
    public $start;
    public $maxDepth;
    public $maxUrls;
    public $maxThreads;
    public $maxTime;
    public $obeyNoFollow;
    public $obeyNoFollowTxt;
    public $loadPage;

    public $urls;
    public $total;
    public $depth;

    public $startTime;

    /**
     * Create Crawler instance
     * @param string $start The starting point for Crawl
     * @param string $loadPage The page that does the actual loading of content.
     * @param bool $obeyNoFollow Follow links with attribute rel="nofollow"?
     * @param int $maxUrls Maximum number of URL to crawl
     * @param int $maxDepth Crawls follow a BFS pattern.  This is the max depth of the resulting BFS tree.
     * @param int $maxThreads Maximum number of parellel CURL requests o make.
     * @param int $timeout Maximum amount of time in seconds Crawler will crawl for.
     */
    public function __construct($start,$loadPage,$obeyNoFollow=true,$maxUrls=999,$maxDepth=3,$maxThreads=10,$timeout=60){
        set_time_limit($timeout+10);

        $this->start=$start;
        $this->maxDepth=$maxDepth;
        $this->obeyNoFollow=$obeyNoFollow;
        $this->maxUrls=$maxUrls;
        $this->loadPage=$loadPage;
        $this->maxThreads=$maxThreads;


        $this->total = 0;
        $this->urls = array();
        $this->obeyNoFollowTxt = ($obeyNoFollow) ? 'true' : 'false';
        $this->startTime = round(microtime(true));
        $this->maxTime = $this->startTime+$timeout;
    }

    /**
     * Begin the crawl
     * @return array
     */
    public function start(){

        $this->urls = array($this->start);
        $this->total++;

        $loader = new PageLoad($this->loadPage);
        $loader->addPage($this->start,$this->obeyNoFollowTxt);

        $this->process($loader);

        return $this->urls;
    }

    private function process(PageLoad &$loader){
        $this->depth++;

        $result = $loader->exec();

        for($i=0;$i<count($result);$i=$i+$this->maxThreads){
            $this->processResult($loader,$result,$i,$i+$this->maxThreads-1);
        }

        if($this->checkContinue() && $loader->getPageCount() > 0)
            $this->process($loader);
    }

    private function processResult(&$loader,&$result,$startIndex,$stopIndex){
        //echo "ProcessResult: [$startIndex,$stopIndex] of ".count($result)."\n";
        for($i=$startIndex;$i<count($result) && $i<=$stopIndex;$i++){
            $this->processResponse($result[$i], $loader);
        }
    }

    private function processResponse(&$response, PageLoad &$loader){
        if(is_object($response) && isset($response->links) && count($response->links) > 0)
            foreach($response->links as $link){
                if(!$this->checkContinue()) break;

                if(!in_array($link,$this->urls)){
                    array_push($this->urls,$link);
                    $loader->addPage($link, $this->obeyNoFollowTxt);
                    $this->total++;
                }
            }
    }

    private function checkContinue(){
        $time = round(microtime(true));
        return (
            $time < $this->maxTime &&
            $this->depth <= $this->maxDepth &&
            $this->total < $this->maxUrls
        );
    }
} 