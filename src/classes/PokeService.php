<?php
class PokeService {
    private $db;
    // private $mailService;

    public function __construct(Database $db) {
        $this->db = $db;
        // $this->mailService = $mailService;
    }

    public function pokeUser($fromUserId, $toUserId) {
        $fromUserQuery = "SELECT username FROM users WHERE id='$fromUserId'";
        $fromUserResult = $this->db->query($fromUserQuery);
        $fromUser = $fromUserResult->fetch_assoc();

        $toUserQuery = "SELECT email, poke_count FROM users WHERE id='$toUserId'";
        $toUserResult = $this->db->query($toUserQuery);
        $toUser = $toUserResult->fetch_assoc();

        if ($toUser) {
            $newPokeCount = $toUser['poke_count'] + 1;
            $updateQuery = "UPDATE users SET poke_count='$newPokeCount' WHERE id='$toUserId'";
            $this->db->query($updateQuery);

            $logPokeQuery = "INSERT INTO poke_history (from_user_id, to_user_id, poke_date) VALUES (?, ?, NOW())";
            $stmt = $this->db->prepare($logPokeQuery);
            $stmt->bind_param("ii", $fromUserId, $toUserId);



            if ($stmt->execute()) {
                $stmt->close();
                
          

                $toEmail = $toUser['email'];
                $subject = "You've been poked!";
                $message = "{$fromUser['username']} pokina tave.";

                // Commented out email sending for now
                // try {
                //     $this->mailService->sendMail($toEmail, $subject, $message);
                //     return ['success' => true, 'new_poke_count' => $newPokeCount];
                // } catch (Exception $e) {
                //     return ['success' => false, 'message' => 'Email could not be sent. ' . $e->getMessage()];
                // }
                
                // Since email sending is commented out, ensure to return success response
                return ['success' => true, 'new_poke_count' => $newPokeCount];
            } else {
                $stmt->close();
                return ['success' => false, 'message' => 'Failed to log poke.'];
            }
        } else {
            return ['success' => false, 'message' => 'User not found'];
        }
    }
}
?>