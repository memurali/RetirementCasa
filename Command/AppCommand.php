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
 * @since     0.2.9
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
require(ROOT.DS. 'vendor' . DS  . 'monkeylearn' . DS . 'autoload.php');
set_time_limit(0);
error_reporting(0);

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link https://book.cakephp.org/4/en/controllers.html#the-app-controller
 */
class AppCommand extends Command
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
	
	public function queue_analysis($url=null)
	{
		$connection = ConnectionManager::get('default');
		try 
		{
			$batchid = '';
			if($url==null)
			{
				$sel_batch_qry = "SELECT `Value` FROM `tblconfig` 
								  WHERE `Key_name`='batch_size'";
				$batch_arr = $connection->execute($sel_batch_qry)->fetchAll('assoc');
				$batch_size = $batch_arr[0]['Value'];
				if($batch_size=='')
					$batch_size = 1;
				$queue_qry = "SELECT * FROM `tblcrawler_queue` WHERE `Status` 
							IN ('submitted') 
							ORDER BY Datecreated DESC 
							LIMIT ".$batch_size;
				
							
			}			
			else
			{
				$queue_qry = 'SELECT * FROM `tblcrawler_queue`
							  WHERE `Url`="'.$url.'"';
			}
			
			$queue_arr = $connection->execute($queue_qry)->fetchAll('assoc');
			if(count($queue_arr)>0)
			{
				
				$a=1;
				$batchid_arr = array_column($queue_arr, 'Url_id');
				$batchid = implode(",", $batchid_arr);
				
				$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='processing' 
								WHERE `Url_id` IN (".$batchid.")";
				$connection->execute($update_qry);
				
				foreach($queue_arr as $queue)
				{
					$urlid = $queue['Url_id'];
					$userid = $queue['Userid'];
					$url = $queue['Url'];
					$stage = $queue['Stage'];
					
					if($stage=='auto_extract')
					{
						
						$sel_qry = "SELECT * FROM `tblarticle` 
									WHERE `Url`='".$url."'";
						$check_arr = $connection->execute($sel_qry)->fetchAll('assoc');
						$count_art = count($check_arr);
						if($count_art==0)
						{
							if ($a % 10 == 0)
							{
								sleep(5);
							}
							$ae_start = microtime(true);//ae = auto extract
							$json = $this->auto_extract($url);
							$ae_end = microtime(true);
							$ae_process_time = ($ae_end - $ae_start);
							$ae_process_time = round($ae_process_time,2);
							
							$json_start = microtime(true);
							$parser_output = $this->json_parser($json);
							$parser_output = json_decode($parser_output);
							
							if($parser_output!='')
							{
								if($parser_output->article!='')
								{
									
									if($parser_output->article->headline!='')
										$article_title = addslashes($parser_output->article->headline);
									else
										$article_title = '';
										
									if($parser_output->article->datePublished!='')
									{
										$article_date_publish = $parser_output->article->datePublished;
										$article_date =  date("Y-m-d", strtotime($article_date_publish));
									}
									else
										$article_date = '';
									
									if($parser_output->article->author!='')
										$author = $parser_output->article->author;
									else
										$author = '';
										
									if($parser_output->article->mainImage!='')
									{
										$image_url = $parser_output->article->mainImage;
										list($width, $height) = getimagesize($image_url); 
										if($width>251 && $height>135)
											$image_url = $image_url;
										else
											$image_url = '';
										
									}
									else
										$image_url = '';
										
									if($parser_output->article->description!='')
									{
										$article_desc = addslashes($parser_output->article->description);
										if(strlen($article_desc)>100)
										{
											$article_desc_pos=strpos($article_desc, ' ', 100);
											if(!is_int($article_desc_pos))
												$article_desc_pos = strrpos($article_desc, ' ');
											$article_desc = substr($article_desc,0,$article_desc_pos); 
										}
									
									}
									else
										$article_desc = '';
										
									if($parser_output->query->domain!='')
										$domain = $parser_output->query->domain;
									else
										$domain = '';
									
									if($article_title!='')
									{
										$domain_id = $this->check_domain($domain);
																			
										$date_created = date('Y-m-d H:i:s');
										$ins_qry = 'INSERT INTO `tblarticle`
														(`Url_id`, `Url`, `Article_title`, 
														`Article_desc`, `Url_image`, `Article_date`, 
														`Author`, `Domain_id`,`Userid`,
														`Thumbs_up`, `Thumbs_down`,Content_type,`Datecreated`) 
													VALUES ('.$urlid.',"'.$url.'","'.$article_title.'",
														"'.$article_desc.'","'.$image_url.'","'.$article_date.'",
														"'.$author.'","'.$domain_id.'",'.$userid.',
														0,0,"article","'.$date_created.'")';
										$connection->execute($ins_qry);
										
										$json_end = microtime(true);
										$json_process_time = ($json_end - $json_start);
										$json_process_time = round($json_process_time,2);
										$process_time = $ae_process_time.'|'.$json_process_time;
										$update_qry = "UPDATE `tblcrawler_queue` SET 
													`Stage`='monkeylearn',
													`Process_time_in_sec`='".$process_time."'
													WHERE `Url_id`=".$urlid;
										$connection->execute($update_qry);
										
										$sel_tag_qry = 'SELECT c.`Tags` FROM `tblclassification` c,tblarticle a WHERE 
														a.`Article_id` = c.`Article_id` AND a.Url_id = '.$urlid;
										$tag_arr = $connection->execute($sel_tag_qry)->fetchAll('assoc');
										if(count($tag_arr)==0)
										{
										
											$monkery_start = microtime(true);
											$tags_result = $this->monkeylearn($article_title);
											//$tags_result = $this->monkeylearn_test($article_title);
											$monkery_end = microtime(true);
											$monkey_time = ($monkery_end - $monkery_start);
											$monkey_time = round($monkey_time,2);
											$dbsave = $this->dbsave($tags_result,$urlid,$monkey_time);
										}
										else
										{
											$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='completed',
																`Stage`='completed' WHERE `Url_id`=".$urlid;
											$connection->execute($update_qry);
										}
									
									}
									else
									{
										
										$update_qry = 'UPDATE `tblcrawler_queue` SET 
												`Status`="error",`Status_description`="empty response from autoextract" 
												WHERE `Url_id`='.$urlid;
										$connection->execute($update_qry);
									}
									
								}
								else if($parser_output->title!='')
								{
									
									$update_qry = 'UPDATE `tblcrawler_queue` SET 
												`Status`="error",`Status_description`="'.$parser_output->title .'" 
												WHERE `Url_id`='.$urlid;
									$connection->execute($update_qry);
								}
								else if($parser_output->error!='')
								{
									
									$update_qry = 'UPDATE `tblcrawler_queue` SET 
												`Status`="error",`Status_description`="'.$parser_output->error .'" 
												WHERE `Url_id`='.$urlid;
									$connection->execute($update_qry);
								}
								else
								{
									$update_qry = 'UPDATE `tblcrawler_queue` SET 
												`Status`="error",`Status_description`="empty response from autoextract" 
												WHERE `Url_id`='.$urlid;
									$connection->execute($update_qry);
								}
								
								
							}
						}
						else
						{
							$update_qry = "UPDATE `tblcrawler_queue` SET `Stage`='monkeylearn' 
										WHERE `Url_id`=".$urlid;
							$connection->execute($update_qry);
							$sel_tag_qry = 'SELECT c.`Tags` FROM `tblclassification` c,tblarticle a WHERE 
														a.`Article_id` = c.`Article_id` AND a.Url_id = '.$urlid;
							$tag_arr = $connection->execute($sel_tag_qry)->fetchAll('assoc');
							if(count($tag_arr)==0)
							{
								
								$article_qry = "SELECT `Article_title` FROM `tblarticle` WHERE `Url_id`=".$urlid;
								$article_arr = $connection->execute($article_qry)->fetchAll('assoc');
								$article_title = $article_arr[0]['Article_title'];
														
								if($article_title!='')
								{
									$monkery_start = microtime(true);
									$tags_result = $this->monkeylearn($article_title);
									//$tags_result = $this->monkeylearn_test($article_title);
									$monkery_end = microtime(true);
									$monkey_time = ($monkery_end - $monkery_start);
									$monkey_time = round($monkey_time,2);
									$dbsave = $this->dbsave($tags_result,$urlid,$monkey_time);
								}
							}
							else
							{
								$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='completed',
													`Stage`='completed' WHERE `Url_id`=".$urlid;
								$connection->execute($update_qry);
							}
							
						}
						
					}
					else if($stage=='monkeylearn')
					{
						$article_qry = "SELECT `Article_id`,`Article_title` FROM `tblarticle` WHERE `Url_id`=".$urlid;
						$article_arr = $connection->execute($article_qry)->fetchAll('assoc');
						$article_title = $article_arr[0]['Article_title'];
						$article_id = $article_arr[0]['Article_id'];
						
						if($article_title!='')
						{
							$sel_tag_qry = 'SELECT `Tags` FROM `tblclassification` WHERE `Article_id`='.$article_id;
							$tag_arr = $connection->execute($sel_tag_qry)->fetchAll('assoc');
							if(count($tag_arr)==0)
							{
								$monkery_start = microtime(true);
								$tags_result = $this->monkeylearn($article_title);
								//$tags_result = $this->monkeylearn_test($article_title);
								$monkery_end = microtime(true);
								$monkey_time = ($monkery_end - $monkery_start);
								$monkey_time = round($monkey_time,2);
								$dbsave = $this->dbsave($tags_result,$urlid,$monkey_time);
							}
							else
							{
								$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='completed',
													`Stage`='completed' WHERE `Url_id`=".$urlid;
								$connection->execute($update_qry);
							}
						}
					}
					
					$a++;
				}

			}
		}
		catch (\Exception $e) 
		{
			$status = $e->getMessage();
		}
		return $batchid;
	}
	public function auto_extract($url)
	{
		$ch = curl_init();
		$username = Configure::read('auto_extract_user');
		$pwd = Configure::read('auto_extract_pass');
		curl_setopt($ch, CURLOPT_URL, 'https://autoextract.scrapinghub.com/v1/extract');
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$pwd");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);
		curl_setopt($ch, CURLOPT_TIMEOUT_MS, 605000);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"url":"'.$url.'", "pageType": "article"}]');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		// $output contains the result
		return $output = curl_exec($ch);
		curl_close($ch);
		
	}
	public function json_parser($json)
	{
		if (!empty($json)) 
		{
			if(json_last_error() === JSON_ERROR_NONE)
			{
				$json = str_replace("\r\n","", $json);
				$json = rtrim($json,']'); 
				$json = ltrim($json,'[');
				//$json = json_decode($json);
				//echo $json->article->headline;
				return $json;
			}
		}
       
        return false;
	}
	public function monkeylearn($title)
	{
		/*$ml = new Client(Configure::read('monkey_clientid'));
		$data = array($title);
		$model_id = Configure::read('monkey_modelid');
		$res = $ml->classifiers->classify($model_id, $data);
		if($res->result!='')
			return $res->result;
		else
			return array();*/
		$connection = ConnectionManager::get('default');
		$tag_id = $this->get_tagid();
		$confidence = $this->get_confidence();
		$conf_val = round($confidence/100,2);
		$match_qry = 'SELECT Kw_Group FROM `tblmatch` 
						WHERE 
							INSTR("'.$title.'", Kw_Phrase)>0  OR 
							INSTR("'.$title.'", REPLACE(Kw_Phrase, " ", ""))>0';
		try 
		{
			$match_arr = $connection->execute($match_qry)->fetchAll('assoc');
			$classification = array();
			if(count($match_arr)>0)
			{
				foreach($match_arr as $kwgrp)
				{
					$classification[] = array(
										'tag_name' => $kwgrp['Kw_Group'],
										'tag_id' => $tag_id,
										'confidence' => $conf_val
										);
				}
						
			}
			else
			{
				$classification[] = array(
										'tag_name' => 'Misc',
										'tag_id' => $tag_id,
										'confidence' => $conf_val
										);
			}
			$array = Array
						(
							Array
								(
									'text' => $title,
									'external_id' => '',
									'error' => '',
									'classifications' => $classification
								)
						);
		}
		catch (\Exception $e) 
		{
			$status = $e->getMessage();
			$array = Array
						(
							Array
								(
									'text' => $title,
									'external_id' => '',
									'error' => $status,
									'classifications' => ''
								)
						);
			
		}
		return $array;
	}
	public function monkeylearn_test($title)
	{
		/*$data = array($title);
		//$rand = rand(1,5);
		$rand = 4;
		if($rand==1)
		{
			
		}
		if($rand==2)
		{
			$res->result = '';
		}
		if($rand==3)
		{
			
			$res->result = Array
				(
					 Array
						(
							'text' => '20 Questions About 529 Plans',
							'external_id' => '',
							'error' => '',
							'classifications' => Array
								(
								)

						)

				);
			
			
		}
		if($rand==4)
		{
			$res->result = Array
			(
				Array
					(
						'text' => '10 Common 401K Questions—Answered',
						'external_id' => '',
						'error' => '',
						'classifications' => Array
							(
								Array
									(
										'tag_name' => 'Test4',
										'tag_id' => 123438928,
										'confidence' => 0.848
									)
							)
					)

			);
		}
		if($rand==5)
		{
			//sleep(500);
			$res->result = Array
			(
				Array
					(
						'text' => '10 Common 401K Questions—Answered',
						'external_id' => '',
						'error' => '',
						'classifications' => Array
							(
								Array
									(
										'tag_name' => 'Test1',
										'tag_id' => 123438928,
										'confidence' => 0.848
									),

								Array
									(
										'tag_name' => 'Test2',
										'tag_id' => 123440760,
										'confidence' => 0.809
									)
							)

					)

			);
		}
		if($res->result!='')
			return $res->result;
		else
			return array();*/
	}
	public function dbsave($tags_result,$urlid,$monkey_time)
	{
		$connection = ConnectionManager::get('default');
		$classification = $tags_result[0]['classifications'];
		
		if(count($classification)>0)
		{
			
			$monkey_process_start = microtime(true);
			$sel_qry = "SELECT q.Process_time_in_sec,a.`Article_id` FROM 
						`tblarticle`a,tblcrawler_queue q WHERE 
						a.`Url_id`= q.`Url_id` AND
						a.`Url_id`=".$urlid;
			$selarr = $connection->execute($sel_qry)->fetchAll('assoc');
			$article_id = $selarr[0]['Article_id'];
			$prev_process_time = $selarr[0]['Process_time_in_sec'];
			$date_created = date('Y-m-d H:i:s');
			for($i=0;$i<count($classification);$i++)
			{
				$tag = str_replace('"', "'", $classification[$i]['tag_name']);
				$tag = addslashes($tag);
				
				/** seo tag ***/
				$seotag = strtolower($tag);
				$seotag=trim($seotag); 
				$seotag = preg_replace('/[^A-Za-z0-9\.]/', '-', $seotag);
				$seotag = preg_replace('/-+/', '-', $seotag);
				$last_tagval = substr($seotag, -1);
				if($last_tagval=='-')
				{
					$seotag = substr($seotag, 0, strlen($seotag)-1);
				}
				
				
				$tagid = $classification[$i]['tag_id'];
				$confidence_float = $classification[$i]['confidence'];
				$confidence = $confidence_float*100;
				$ins_qry = 'INSERT INTO `tblclassification`
								(`Article_id`, `Tags`,`SEO_Tag`, `Tag_id`, `Confidence`,`Datecreated`) 
							VALUES ('.$article_id.',"'.trim($tag).'","'.$seotag.'",'.$tagid.',"'.$confidence.'","'.$date_created.'")';
				$connection->execute($ins_qry);	
				
			}
			
			$monkey_process_end = microtime(true);
			$monkey_process_time = ($monkey_process_end - $monkey_process_start);
			$monkey_process_time = round($monkey_process_time,2);
			$new_process_time = $prev_process_time.'|'.$monkey_time.'|'.$monkey_process_time;
			
			$update_qry = "UPDATE `tblcrawler_queue` SET `Status`='completed',
						  `Stage`='completed',`Process_time_in_sec`='".$new_process_time."'
						  WHERE `Url_id`=".$urlid;
			$connection->execute($update_qry);
		}
		
		else if($tags_result[0]['error_detail']!='')
		{
			
			$update_qry = 'UPDATE `tblcrawler_queue` SET `Status`="error",
						  `Status_description`="'.$tags_result[0]['error_detail'].'"
						  WHERE `Url_id`='.$urlid;
			$connection->execute($update_qry);
		}
		else
		{
			$update_qry = 'UPDATE `tblcrawler_queue` SET `Status`="error",
						  `Status_description`="empty response from monkeylearn"
						  WHERE `Url_id`='.$urlid;
			$connection->execute($update_qry);
			
		}	
		
	}
	public function check_domain($domain)
	{
		
		$connection = ConnectionManager::get('default');
		if($domain!='')
		{
			$domain = addslashes($domain);
			$sel_dist_qry = "SELECT `Domain_id` FROM `tblranking_score` 
							WHERE `Domain_name`='".$domain."'";
			$seldist_arr = $connection->execute($sel_dist_qry)->fetchAll('assoc');
			if(count($seldist_arr)>0)
			{
				$domain_id = $seldist_arr[0]['Domain_id'];
			}
			else
			{
				$sel_dscore_qry = "SELECT `Value` FROM `tblconfig` WHERE `Key_name`='domain_score'";
				$seldscore_arr = $connection->execute($sel_dscore_qry)->fetchAll('assoc');
				$dscore = $seldscore_arr[0]['Value'];
				if($dscore=='')
					$dscore = 0.5;
				$date_created = date('Y-m-d H:i:s');
				$ins_qry = "INSERT INTO `tblranking_score`(`Domain_name`, `Domain_score`,`Datecreated`) 
							VALUES ('".$domain."',".$dscore.",'".$date_created."')";
				$connection->execute($ins_qry);	
				$sel_domainid_qry = "SELECT `Domain_id` FROM `tblranking_score` 
									ORDER BY `Domain_id` DESC LIMIT 1";
				$seldomain_arr = $connection->execute($sel_domainid_qry)->fetchAll('assoc');
				$domain_id = $seldomain_arr[0]['Domain_id'];
			}
			if($domain_id=='')
				$domain_id = 1;
		}
		else
		{
			$sel_qry = "SELECT `Domain_id` FROM `tblranking_score` WHERE 
						`Domain_name` = 'emptydomain.com'";
			$seldomain_arr = $connection->execute($sel_qry)->fetchAll('assoc');
			if(count($seldomain_arr)>0)
			{
				$domain_id = $seldomain_arr[0]['Domain_id'];
			}
			else
			{
				$sel_dscore_qry = "SELECT `Value` FROM `tblconfig` WHERE `Key_name`='domain_score'";
				$seldscore_arr = $connection->execute($sel_dscore_qry)->fetchAll('assoc');
				$dscore = $seldscore_arr[0]['Value'];
				if($dscore=='')
					$dscore = 0.5;
				$date_created = date('Y-m-d H:i:s');
				$ins_qry = "INSERT INTO `tblranking_score`(`Domain_name`, `Domain_score`,`Datecreated`) 
							VALUES ('emptydomain.com',".$dscore.",'".$date_created."')";
				$connection->execute($ins_qry);	
				$sel_domainid_qry = "SELECT `Domain_id` FROM `tblranking_score` 
									ORDER BY `Domain_id` DESC LIMIT 1";
				$seldomain_arr = $connection->execute($sel_domainid_qry)->fetchAll('assoc');
				$domain_id = $seldomain_arr[0]['Domain_id'];
			}
		}
		return $domain_id;
		
	}
	
	public function get_tagid()
	{
		$connection = ConnectionManager::get('default');
		$config_qry = "SELECT `Value` FROM `tblconfig` WHERE `Key_name`='default_tagid'";
		$config_arr = $connection->execute($config_qry)->fetchAll('assoc');
		if($config_arr[0]['Value']!='')
			$tagid = $config_arr[0]['Value'];
		else
			$tagid = 0;
		return $tagid;
	}
	public function get_confidence()
	{
		$connection = ConnectionManager::get('default');
		$config_qry = "SELECT `Value` FROM `tblconfig` WHERE `Key_name`='confidence'";
		$config_arr = $connection->execute($config_qry)->fetchAll('assoc');
		if($config_arr[0]['Value']!='')
			$confidence = $config_arr[0]['Value'];
		else
			$confidence = 0;
		return $confidence;
	}
	
}
