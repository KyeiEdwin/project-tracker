<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        // Add unique constraint: Only one active sprint per project
        // Note: SQLite doesn't support partial indexes, so we'll handle this in application logic for SQLite
        if ($driver === 'mysql') {
            DB::statement('
                CREATE UNIQUE INDEX idx_one_active_sprint_per_project 
                ON sprints(project_id) 
                WHERE status = "active" AND deleted_at IS NULL
            ');
            
            // Create trigger to prevent updates to completed sprints
            DB::unprepared('
                CREATE TRIGGER prevent_completed_sprint_updates
                BEFORE UPDATE ON sprints
                FOR EACH ROW
                BEGIN
                    IF OLD.status = "completed" THEN
                        SIGNAL SQLSTATE "45000" 
                        SET MESSAGE_TEXT = "Cannot modify completed sprint";
                    END IF;
                END
            ');
        } elseif ($driver === 'pgsql') {
            DB::statement('
                CREATE UNIQUE INDEX idx_one_active_sprint_per_project 
                ON sprints(project_id) 
                WHERE status = \'active\' AND deleted_at IS NULL
            ');
            
            // PostgreSQL trigger
            DB::unprepared('
                CREATE OR REPLACE FUNCTION prevent_completed_sprint_updates()
                RETURNS TRIGGER AS $$
                BEGIN
                    IF OLD.status = \'completed\' THEN
                        RAISE EXCEPTION \'Cannot modify completed sprint\';
                    END IF;
                    RETURN NEW;
                END;
                $$ LANGUAGE plpgsql;
                
                CREATE TRIGGER prevent_completed_sprint_updates
                BEFORE UPDATE ON sprints
                FOR EACH ROW
                EXECUTE FUNCTION prevent_completed_sprint_updates();
            ');
        }
        // For SQLite, we'll rely on application-level validation via observers
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement('DROP TRIGGER IF EXISTS prevent_completed_sprint_updates');
            DB::statement('DROP INDEX IF EXISTS idx_one_active_sprint_per_project ON sprints');
        } elseif ($driver === 'pgsql') {
            DB::statement('DROP TRIGGER IF EXISTS prevent_completed_sprint_updates ON sprints');
            DB::statement('DROP FUNCTION IF EXISTS prevent_completed_sprint_updates');
            DB::statement('DROP INDEX IF EXISTS idx_one_active_sprint_per_project');
        }
    }
};
