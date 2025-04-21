<?php

namespace app\models;

class Workout extends Model
{
    protected $table = 'workouts';

    // Get logs by user
    public function getLogsByUser($user_id)
    {
        return $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = ? ORDER BY date DESC",
            [$user_id]
        );
    }

    // Get logs by exercise (case-insensitive search)
    public function getLogsByExercise($user_id, $exercise)
    {
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE user_id = ? AND exercise COLLATE utf8_general_ci LIKE ? 
             ORDER BY date DESC",
            [$user_id, "%$exercise%"]
        );
    }

    // Save new log
    public function saveLog($data)
    {
        return $this->query(
            "INSERT INTO {$this->table} 
             (date, exercise, weight, sets, reps, time, distance, calories, user_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['date'],
                $data['exercise'],
                $data['weight'],
                $data['sets'],
                $data['reps'],
                $data['time'],
                $data['distance'],
                $data['calories'],
                $data['user_id']
            ]
        );
    }

    // Update log
    public function updateLog($data)
    {
        return $this->query(
            "UPDATE {$this->table} 
             SET date = ?, exercise = ?, weight = ?, sets = ?, reps = ?, time = ?, distance = ?, calories = ? 
             WHERE id = ? AND user_id = ?",
            [
                $data['date'],
                $data['exercise'],
                $data['weight'],
                $data['sets'],
                $data['reps'],
                $data['time'],
                $data['distance'],
                $data['calories'],
                $data['id'],
                $data['user_id']
            ]
        );
    }

    // Delete log
    public function deleteLog($id, $user_id)
    {
        return $this->query(
            "DELETE FROM {$this->table} WHERE id = ? AND user_id = ?",
            [$id, $user_id]
        );
    }
}
