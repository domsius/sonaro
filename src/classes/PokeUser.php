<?php
class PokeUser {
    public function poke($userId) {
        // Implement the actual poking logic here.
        // For now, we'll simulate a successful poke.
        return [
            'success' => true,
            'new_poke_count' => rand(1, 100) // Simulated poke count.
        ];
    }
}
?>