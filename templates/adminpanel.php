		<div id="bottomHalf" style="font-family:serif">
			<h3>Users</h3>
				<table>
					<tr>
						<th>ID</th>
						<th>username</th>
						<th>status</th>
					</tr>
					<?php
                    /**
                     * @var array $params
                     * @var \HeraldryEngine\Application $app
                     * @var \HeraldryEngine\Dbo\User $user
                     */
						foreach($params['users'] as $user) {
						    $accessLevel = $user->getAccessLevel();
								if($accessLevel == ACCESS_LEVEL_ADMIN)
									$status="admin";
								elseif ($accessLevel == ACCESS_LEVEL_USER)
									$status="standard";
								elseif ($accessLevel == ACCESS_LEVEL_NONE)
									$status="disabled";
								else
									$status="unknown";
								$id = $user->getID();
								$name = $user->getUserName();
						?>
							<tr>
								<td><?=$id?></td>
								<td><?=$name?></td>
								<td><?=$status?></td>
								<td><!--suppress HtmlUnknownTarget -->
                                    <a href="/admin/changepassword/<?=$id?>">Change password</a>
								</td>
								<td><a href="/admin/deleteuser/<?=$id?>" data-post >Delete user</a>
								</td>
								<td><?php
									if($accessLevel!=ACCESS_LEVEL_ADMIN):
									?><a href="/admin/setaccess/0/<?=$id?>" data-post >Promote user</a>
								<?php else: ?>
										Promote user
								<?php endif ?></td>
								<td>
<?php if($accessLevel<ACCESS_LEVEL_USER): ?>
									<a href="/admin/setaccess/1/<?=$id?>" data-post >Demote user</a>
<?php elseif($accessLevel>ACCESS_LEVEL_USER): ?>
									<a href="/admin/setaccess/1/<?=$id?>" data-post >Enable user</a>
<?php else: ?>
									Demote user
<?php endif ?>
								</td>
								<td><?php
									if($accessLevel!=ACCESS_LEVEL_NONE):
									?><a href="/admin/setaccess/2/<?=$id?>" data-post >Disable user</a>
								<?php else: ?>
										Disable user
								<?php endif ?></td>
							</tr>
					<?php
						}
					?>
				</table>
				<h3>Authenticated sessions</h3>
				<table>
					<tr>
						<th>Session ID</th>
						<th>User name</th>
						<th>User IP</th>
						<th>Location</th>
						<th>Time created</th>
						<th>Time expires</th>
						<th>Browser</th>
						<th>OS</th>
						<th></th>
					</tr>
					<?php
                    /**
                     * @var \Symfony\Component\HttpFoundation\Session\Session $session
                     */
                    $userRepo = $app['entity_manager']->getRepository(\HeraldryEngine\Dbo\User::class);
					foreach($params['sessions'] as $id => $session){
					    $id = $session->getName();
					    if( $session->has('expiry') && !($session->get('expiry') instanceof \DateTime) ){
					        $dt = new DateTime();
					        $dt->setTimestamp($session->get('expiry'));
					        $session->set('expiry',$dt);
                        }
						if($session->has('userID')){
							if(!$session->has('expiry')
								|| $session->get('expiry')->getTimestamp() >= $app['clock']->__invoke()->getTimestamp()){
					?>
					<tr>
						<td><?=$session->getName()?></td>
						<td><?php
						if($session->has('userID')){
						    $userID = $session->get('userID');
							if( $userID >= 0){
                                $user = $userRepo->find($userID);
                                if(isset($user)) {
                                    echo $user->getUserName();
                                }else{
                                    echo "Unknown user";
                                }
							}else{
								echo "Corrupted Session";
							}
						}
						?></td>
						<td><?php
							if($session->has('userIP')){
								echo $session->get("userIP");
							}else{
								echo "unknown";
							}
							echo "<!--".$session->get("geoIP")."-->";
						?></td>
						<td><?php
						if($session->has('city')){
							echo $session->get("city").", ";
						}
						if($session->has('countryName')){
							echo str_replace(" ","&nbsp;",
								$session->get("countryName"));
						}else{
							echo "unknown";
						}
						?></td>
						<td><?php
							if($session->has('startTime')){
								date_default_timezone_set('Europe/London');
								echo $session->get("startTime")->format('d/m/Y H:i:s');
							}else{
								echo "Unknown";
							}
						?></td>
						<td><?php
							if($session->has('expiry')){
								date_default_timezone_set('Europe/London');
								echo $session->get("expiry")->format('d/m/Y H:i:s');
							}else{
								echo "Never";
							}
						?></td>
						<td><?php
							if($session->has('browser')){
								echo $session->get("browser");
							}else{
								echo "unknown";
							}
						?></td>
						<td><?php
							if($session->has('OS')){
								echo str_replace(" ", "&nbsp;", $session->get("OS"));
							}else{
								echo "unknown";
							}
						?></td>
                        <!-- TODO: remove dependence on global state -->
						<td><?php if($id!=session_id()):?><a href="/deletesession/<?=$id?>" data-post >
							Delete session</a><?php endif ?>
					</tr>
					<?php
							}
						}
					}
				?></table>
				<p>
					<!--suppress HtmlUnknownTarget -->
                    <a href="/admin/collectgarbage" data-post >Collect garbage</a>
				</p>
			</div>
