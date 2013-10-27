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