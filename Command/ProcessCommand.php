<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Command;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Log\Log;
use Psy\Configuration as PsyConfiguration;
use Psy\Shell as PsyShell;
use Cake\Datasource\ConnectionManager;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\ORM\Table;
use Cake\ORM\Locator\LocatorAwareTrait;
use MonkeyLearn\Client;
set_time_limit(0);
error_reporting(0);
/**
 * Simple console wrapper around Psy\Shell.
 */
class ProcessCommand extends AppCommand
{
    /**
     * Start the Command and interactive console.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null|void The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
		$connection = ConnectionManager::get('default');
		$this->autoLayout = false;
        $start_time = 'Start_time : '.date("Y-m-d H:i:s").';';
		$process_start = microtime(true);
        $queue = parent::queue_analysis();
		$process_end = microtime(true);
		$endtime= ' End_time : '.date("Y-m-d H:i:s").';';
		$tot_process_time = ($process_end - $process_start);
		$tot_process_time = round($tot_process_time,2);
		$duration= ' Duration : '.gmdate('H:i:s', (int)$tot_process_time).PHP_EOL;
        if($queue!='')
		{
			$sel_resub = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` WHERE 
						`Status`='processing' AND Url_id IN (".$queue.")
						LIMIT 1";
			$sel_resub_arr = $connection->execute($sel_resub)->fetchAll('assoc');
            if($sel_resub_arr[0]['count']>0)
                $resubmit_count= 'Total number of resubmitted url : '.$sel_resub_arr[0]['count'].PHP_EOL;
			else
				$resubmit_count= '';	
			
			$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='submitted' 
							WHERE `Url_id` IN (".$queue.") AND `Status`='processing'";
			
			$connection->execute($update_qry);
			$batch_size = 'Batch count : '.count(explode(',',$queue)).PHP_EOL;
			$sel_comp = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` WHERE 
						`Stage`='completed' AND Url_id IN (".$queue.")
						LIMIT 1";
			$comp_arr = $connection->execute($sel_comp)->fetchAll('assoc');
            if($comp_arr[0]['count']!='')
                $comp_count= 'Total completed count(Auto_extract+Monkeylearn) : '.$comp_arr[0]['count'].PHP_EOL;
			else
				$comp_count= '';
			
			/*$sel_comp_auto = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` 
							WHERE `Stage`='monkeylearn' AND Url_id IN (".$queue.") LIMIT 1";
			$auto_comp_arr = $connection->execute($sel_comp_auto)->fetchAll('assoc');
            if($auto_comp_arr[0]['count']!='')
                $results.= 'Total auto extract only completed :'.$auto_comp_arr[0]['count'].',';*/

            $sel_ae_error = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` 
							WHERE `Status`='error' AND Stage = 'auto_extract' 
							AND Url_id IN (".$queue.") LIMIT 1";
			$ae_error_arr = $connection->execute($sel_ae_error)->fetchAll('assoc');
			$ae_err_count = $ae_error_arr[0]['count'];
            if($ae_err_count>0)
                $ae_error = '	Auto Extract : '.$ae_err_count.PHP_EOL;
			else
				$ae_error = '';
			
			$sel_ml_error = "SELECT COUNT(`Url`) as count FROM `tblcrawler_queue` 
							WHERE `Status`='error' AND Stage = 'monkeylearn' 
							AND Url_id IN (".$queue.") LIMIT 1";
			$ml_error_arr = $connection->execute($sel_ml_error)->fetchAll('assoc');
			$ml_error_count = $ml_error_arr[0]['count'];
            if($ml_error_count>0)
                $ml_error = '	Monkeylearn : '.$ml_error_count.PHP_EOL;
			else
				$ml_error = '';
            
            $results = $start_time.$endtime.$duration.$batch_size.$comp_count;
			if($ae_err_count>0 || $ml_error_count>0)
			{
				$error = 'Error:'.PHP_EOL;
				$error.= $ae_error.$ml_error;
			}
            else
				$error = '';
			
			echo $results.$error.$resubmit_count;
			
            /*if($results!='')
            {
                $fp = fopen('../bin/cron.log','a') or die("Unable to open cron log file!");
                fwrite($fp, "\r\n".$results);  
                fclose($fp);  
            }*/
		}
    }

    /**
     * Display help for this console.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to update
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription(
            'This shell provides a REPL that you can use to interact with ' .
            'your application in a command line designed to run PHP code. ' .
            'You can use it to run adhoc queries with your models, or ' .
            'explore the features of CakePHP and your application.' .
            "\n\n" .
            'You will need to have psysh installed for this Shell to work.'
        );

        return $parser;
    }
}
