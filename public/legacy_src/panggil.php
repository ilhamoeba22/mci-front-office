
<?php
  $antri = $_GET['id'];
  $type = $_GET['jns'];
  $aaaa = explode("-",$antri);
  $antrian  = intval($aaaa[1]);
?>
<script type="text/javascript">
    $(function(){
      $("#play").click(function(){
        document.getElementById('suarabel').play();
        document.getElementById('suarabelnomorurut').play();
        document.getElementById('suarabelsuarabelloket').play();
      });

      $("#pause").click(function(){
        document.getElementById("suarabel").pause();
      });

      $("#stop").click(function(){
        document.getElementById("suarabel").pause();
        document.getElementById("suarabel").currentTime=0;
      });
    })
</script>
<audio id="suarabel" src="audio/Airport_Bell.mp3"></audio>
<audio id="suarabelnomorurut" src="audio/antrian/nomor-urut.wav"></audio> 
<audio id="diloket" src="audio/antrian/loket.wav"></audio>
<?php
if($antrian > 11 && $antrian < 20){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, -1,1).'.wav'; ?>"></audio>
<?php }
else if($antrian == 20){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, 0,1).'.wav'; ?>"></audio>
<?php }
else if($antrian > 20 && $antrian < 100){ ?> 
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, 0,1).'.wav'; ?>"></audio>
	<?php
	$a=substr($antrian, -1,1);
	if($a == 0){

	}
	else{?>
		<audio id="antrian1" src="<?php echo 'audio/antrian/'.$a.'.wav'; ?>"></audio>
<?php 
	}
}
else if($antrian > 100 && $antrian < 110){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, -1,1).'.wav'; ?>"></audio>
<?php 
}
else if($antrian > 111 && $antrian < 120){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, -1,1).'.wav'; ?>"></audio>
<?php 
 
}
else if($antrian > 119 && $antrian < 210){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, 0,1).'.wav'; ?>"></audio>
	<?php
	$a=substr($antrian, -1,1);
	if($a == 0){

	}
	else{?>
		<audio id="antrian1" src="<?php echo 'audio/antrian/'.$a.'.wav'; ?>"></audio>
<?php 
	}
}
else if($antrian == 210){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, 0,1).'.wav'; ?>"></audio>
<?php }
else if($antrian == 211){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, 0,1).'.wav'; ?>"></audio>
<?php }
else if($antrian > 211 && $antrian < 220){ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, 0,1).'.wav'; ?>"></audio>
	<audio id="antrian1" src="<?php echo 'audio/antrian/'.substr($antrian, -1,1).'.wav'; ?>"></audio>
<?php 
}
else if($antrian > 219 && $antrian < 1000){ ?> 
	<audio id="antrian" src="<?php echo 'audio/antrian/'.substr($antrian, 0,1).'.wav'; ?>"></audio>
<?php
	$a = substr($antrian, 1,1);
	$b = substr($antrian, -1,1);
		echo "<audio id='antrian1' src='".'audio/antrian/'.$a.".wav'></audio>";
		echo "<audio id='antrian2' src='".'audio/antrian/'.$b.".wav'></audio>";
}
else{ ?>
	<audio id="antrian" src="<?php echo 'audio/antrian/'.$antrian.'.wav'; ?>"></audio>
<?php } ?>

<audio id="loket<?php echo $antrian; ?>" src="audio/Airport_Bell.mp3"></audio>
<audio id="sepuluh" src="audio/antrian/sepuluh.wav"></audio>
<audio id="sebelas" src="audio/antrian/sebelas.wav"></audio>
<audio id="seratus" src="audio/antrian/seratus.wav"></audio>
<audio id="belas" src="audio/antrian/belas.wav"></audio>
<audio id="puluh" src="audio/antrian/puluh.wav"></audio>
<audio id="ratus" src="audio/antrian/ratus.wav"></audio>

