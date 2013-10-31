<div class="outerHead">
	<div class="head">
    	<?php echo CONFIG::$APPNAME; ?>
    </div>
</div>
<div class="mainCont">
	
<div class="innerCont">
	<div class="subhead">
    	<h3>Config</h3>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$REQ; ?>">Request</a>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$LOGS; ?>">Log</a>
    </div>
    <div class="subhead">
    	<a class="button" href="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$LGOUTSCRIPT; ?>">Logout</a>
    </div>
    <div style="clear:both"></div>
    <hr />
    <div>
        <a name="general"></a>
        <h4>General Settings</h4>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="credentials" />
        <input type="hidden" name="method" value="edit" />
        <table>
            <tr>
            	<td>
                <strong>Username:</strong>
                <input type="text" name="usr" value="<?php echo $at->getUsername(); ?>" />
                </td>
                <td>
                    <strong>Password:</strong>
                <input type="password" name="pwd" value="" />
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <a name="lastfm"></a>
        <h4>Last.FM</h4>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="lastfm" />
        <input type="hidden" name="method" value="edit" />
        <table>
            <tr>
            	<td>
                <strong>Last FM ApiKey:</strong>
                <input type="text" name="apikey" value="<?php echo $conf->getLastfmApiKey(); ?>" />
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <div style="display:none;">
        <h4>Sabnzbd</h4>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="sabnzbd" />
        <input type="hidden" name="method" value="edit" />
        <table>
        	<tr>
            	<td colspan="2">
                <strong>Enabled: </strong>
                <input name="enabled" type="checkbox" <?php if($sab['enabled']) echo "checked"; ?> value="true" />
                <strong>https: </strong>
                <input name="https" type="checkbox" <?php if($sab['https']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Server:</strong>
                <input type="text" name="url" value="<?php echo $sab['server']; ?>" />
                </td>
                <td>
                <strong>Port:</strong>
                <input type="text" name="port" value="<?php echo $sab['port']; ?>" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>ApiKey:</strong>
                <input type="text" name="apikey" value="<?php echo $sab['apikey']; ?>" />
                </td>
                <td>
                <strong>Category:</strong>
                <input type="text" name="cat" value="<?php echo $sab['category']; ?>" />
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        </div>
        <h4>Headphones</h4>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="hp" />
        <input type="hidden" name="method" value="edit" />
        <table>
        	<tr>
            	<td colspan="2">
                <strong>Enabled: </strong>
                <input name="enabled" type="checkbox" <?php if($hp['enabled']) echo "checked"; ?> value="true" />
                <strong>https: </strong>
                <input name="https" type="checkbox" <?php if($hp['https']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Server:</strong>
                <input type="text" name="url" value="<?php echo $hp['server']; ?>" />
                </td>
                <td>
                <strong>Port:</strong>
                <input type="text" name="port" value="<?php echo $hp['port']; ?>" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>ApiKey:</strong>
                <input type="text" name="apikey" value="<?php echo $hp['apikey']; ?>" />
                </td>
                <td>
                <strong>Backlog Status:</strong>
                <select size="1" name="bklog">
                	<option value="wanted" <?php if($hp["bklog"] == "wanted") echo "selected"; ?>>Wanted</option>
                    <option value="skipped" <?php if($hp["bklog"] == "skipped") echo "selected"; ?>>Skipped</option>
                    <option value="archived" <?php if($hp["bklog"] == "archived") echo "selected"; ?>>Archived</option>
                    <option value="ignored" <?php if($hp["bklog"] == "ignored") echo "selected"; ?>>Ignored</option>
                </select>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <h4>Couchpotato</h4>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="cp" />
        <input type="hidden" name="method" value="edit" />
        <table>
        	<tr>
            	<td colspan="2">
                <strong>Enabled: </strong>
                <input name="enabled" type="checkbox" <?php if($cp['enabled']) echo "checked"; ?> value="true" />
                <strong>https: </strong>
                <input name="https" type="checkbox" <?php if($cp['https']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Server:</strong>
                <input type="text" name="url" value="<?php echo $cp['server']; ?>" />
                </td>
                <td>
                <strong>Port:</strong>
                <input type="text" name="port" value="<?php echo $cp['port']; ?>" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>ApiKey:</strong>
                <input type="text" name="apikey" value="<?php echo $cp['apikey']; ?>" />
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <h4>Sickbeard</h4>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="sb" />
        <input type="hidden" name="method" value="edit" />
        <table>
        	<tr>
            	<td colspan="2">
                <strong>Enabled: </strong>
                <input name="enabled" type="checkbox" <?php if($sb['enabled']) echo "checked"; ?> value="true" />
                <strong>https: </strong>
                <input name="https" type="checkbox" <?php if($sb['https']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Server:</strong>
                <input type="text" name="url" value="<?php echo $sb['server']; ?>" />
                </td>
                <td>
                <strong>Port:</strong>
                <input type="text" name="port" value="<?php echo $sb['port']; ?>" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>ApiKey:</strong>
                <input type="text" name="apikey" value="<?php echo $sb['apikey']; ?>" />
                </td>
                <td>
                <strong>Backlog Status:</strong>
                <select size="1" name="bklog">
                	<option value="wanted" <?php if($sb["bklog"] == "wanted") echo "selected"; ?>>Wanted</option>
                    <option value="skipped" <?php if($sb["bklog"] == "skipped") echo "selected"; ?>>Skipped</option>
                    <option value="archived" <?php if($sb["bklog"] == "archived") echo "selected"; ?>>Archived</option>
                    <option value="ignored" <?php if($sb["bklog"] == "ignored") echo "selected"; ?>>Ignored</option>
                </select>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <h4>Email Notification</h4>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="email" />
        <input type="hidden" name="method" value="edit" />
        <table>
        	<tr>
            	<td>
                <strong>Enabled: </strong>
                <input name="enabled" type="checkbox" <?php if($email['enabled']) echo "checked"; ?> value="true" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Recieving address:</strong>
                <input type="text" name="to" value="<?php echo $email['to']; ?>" />
                </td>
                <td>
                <strong>Sending address:</strong>
                <input name="from" type="text" value="<?php echo $email['from']; ?>" />
                </td>
            </tr>
            <tr>
            	<td>
                <strong>Subject:</strong>
                <input type="text" name="subject" value="<?php echo $email['subject']; ?>" />
                </td>
                <td>
                </td>
            </tr>
            <tr>
            	<td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        <br />
        <div style="display:none;">
        <h4>Indexers</h4>
        <?php 
		echo $sroot.CONFIG::$DBS.INDEXSITE::$dbfile ."test";
		$i=0;
		$nextindex=0;
		if($indexers === true){
			for($i; $i<count($indexsites); $i++){ 
				$inx = $indexsites[$i] ?>
                
                <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
                <input type="hidden" name="t" value="indexsite" />
                <input type="hidden" name="method" value="edit" />
                <input type="hidden" name="index" value="<?php echo $inx->getId(); ?>" />
                <table>
                    <tr>
                        <td>
                        <strong>Name:</strong>
                        <input type="text" name="name" value="<?php echo $inx->getName(); ?>" />
                        </td>
                        <td>
                        <strong>Enabled: </strong><input name="enabled" type="checkbox" <?php if($inx->isEnabled()) echo "checked"; ?> value="true" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <strong>Url:</strong>
                        <input type="text" name="url" value="<?php echo $inx->getUrl(); ?>" />
                        </td>
                        <td>
                        <strong>ApiKey:</strong>
                        <input type="text" name="apikey" value="<?php echo $inx->getApiKey(); ?>" />
                        </td>
                    </tr>
                    <tr>
                    	<td>
                        <strong>Category:</strong>
                        <input type="text" name="cat" value="<?php echo $inx->getCat(); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                        <input type="submit" value="save" />
                       
                        </td>
                   </tr>
                </table>
                </form>
                <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
                    <input type="hidden" name="t" value="indexsite" />
                    <input type="hidden" name="method" value="delete" />
                    <input type="hidden" name="index" value="<?php echo $inx->getId(); ?>" />
                	<input type="submit" value="delete" />
                </form>
                <br />
				<?php
				$nextindex = intval($inx->getId())+1;
			}
		} ?>
        <form method="post" action="<?php echo $root.CONFIG::$SCRIPTS.CONFIG::$CHSCRIPT; ?>" enctype="multipart/form-data">
        <input type="hidden" name="t" value="indexsite" />
        <input type="hidden" name="method" value="add" />
        <input type="hidden" name="index" value="<?php echo $nextindex; ?>" />
        <table>
            <tr>
                <td>
                <strong>Name:</strong>
                <input type="text" name="name" value="" />
                </td>
                <td>
                <strong>Enabled: </strong><input name="enabled" type="checkbox" value="true" />
                </td>
            </tr>
            <tr>
                <td>
                <strong>Url:</strong>
                <input type="text" name="url" value="" />
                </td>
                <td>
                <strong>ApiKey:</strong>
                <input type="text" name="apikey" value="" />
                </td>
            </tr>
            <tr>
                <td>
                <strong>Category:</strong>
                <input type="text" name="cat" value="3010" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                <input type="submit" value="save" />
               
                </td>
           </tr>
        </table>
        </form>
        </div>
    </div>
</div>
</div>