<?php
class RoboBlocks {
    private $blocks;
    private $block_index;

    function RoboBlocks($limit) {
        $this->blocks = $this->initialize_blocks($limit);
        $this->block_index = $this->initialize_block_index($limit);
    }

    public function move_onto($b, $a) {
        if ($this->block_index[$a] != $this->block_index[$b]) {
            $this->clear_over($a);
            $this->move($b, $a);
        }
    }

    public function move_over($b, $a){
        if ($this->block_index[$a] != $this->block_index[$b]) {
            $this->move($b, $a);
        }
    }

    public function pile_onto($b, $a) {
        if ($this->block_index[$a] != $this->block_index[$b]) {
            $this->clear_over($a);
            $this->pile($b, $a);
        }
    }

    public function pile_over($b, $a) {
        if ($this->block_index[$a] != $this->block_index[$b]) {
            $this->pile($b, $a);
        }
    }

    public function get_blocks() {
        return $this->blocks;
    }

    public function __toString() {
        $output = "";
            foreach ($this->blocks as $row => $stack) {	
                $output .= "$row:";
                foreach($stack as $block) {
                    $output .= " $block";
                }
                $output .= "\n";
            }
            return $output;
        }
    
    private function initialize_blocks($limit) {

        $blocks = [];
        foreach (range(0, $limit-1) as $i) {
	        $blocks[$i] = [$i];
        }
        return $blocks;
    }

    private function initialize_block_index($limit) {
        // populates the location index with starting positions
        $block_index = [];
        foreach (range(0, $limit - 1) as $i) {
            $block_index[$i] = $i;
        }
        return $block_index;
    }

    private function return_home($b) {
        // find block, pop it from current location, move it home, and update index
        $location_b = $this->block_index[$b];
        array_pop($this->blocks[$location_b]);
        array_push($this->blocks[$b], $b);
        $this->block_index[$b] = $b;
    }

    private function move($b, $a) {
        // locate both blocks
        $location_b = $this->block_index[$b];
        $location_a = $this->block_index[$a];

        // clear anything over b, pop b, append over a, and update location index
        $this->clear_over($b);
        array_pop($this->blocks[$location_b]);
        array_push($this->blocks[$location_a], $b);

        //reset indexes
        $this->blocks[$location_a] = array_values($this->blocks[$location_a]);
        
        $this->block_index[$b] = $location_a;
    }

    private function pile($b, $a) {
        // get the location of the blocks
        $location_b = $this->block_index[$b];
        $location_a = $this->block_index[$a];
        
        // find the index of block b in its current stack
        $index_b = array_search($b, $this->blocks[$location_b]);

        // get block b and everyone on top, and move them over block a
        $block_stack = array_slice($this->blocks[$location_b], $index_b, NULL, True);
        $this->blocks[$location_a] = array_merge($this->blocks[$location_a], $block_stack);

        // since slice doesn't pop the values, remove them manually
        for($i = count($this->blocks[$location_b]) - 1; $i >=$index_b; $i--){
            unset($this->blocks[$location_b][$i]);
        }

        //reset indexes
        $this->blocks[$location_a] = array_values($this->blocks[$location_a]);

        // update the index of each block with new location
        foreach($block_stack as $block) {
            $this->block_index[$block] = $location_a;
        }
    }

    private function clear_over($a) {
        // find location of block
        $location_a = $this->block_index[$a];
        
        // loop over stack in reverse and pop everything over
        // the block and return them home
        $stack_size = count($this->blocks[$location_a]);
        for($i = $stack_size - 1; $i>=0; $i--) {
            $block = $this->blocks[$location_a][$i];
            if ($block == $a) {
                break;
            } else {
                $this->return_home($block);
            }
        }
    }
}
?>