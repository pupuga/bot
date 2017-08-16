<div class="message-bank">
    <div class="info">
        <div class="image">
            <a href="<?php echo $object->track ?>" target="_blank"><img src="<?php echo $object->logo ?>" alt="<?php echo $object->name ?>"></a>
        </div>
        <div class="sum">
            <div class="label"><?php _e('DU KAN SPARE') ?></div>
            <div class="profit">
                <div class="value"><?php echo $this->getProfit() ?></div>
                <div class="units"><?php echo (isset($this->values['debt']['units']) ? $this->values['debt']['units'] : '') ?></div>
            </div>
        </div>
    </div>
    <div class="description"><?php echo $object->description ?></div>
    <div class="link"><a href="<?php echo $object->track ?>" target="_blank"><?php _e('Les mer hos') ?> <?php echo $object->name ?></a></div>
</div>
