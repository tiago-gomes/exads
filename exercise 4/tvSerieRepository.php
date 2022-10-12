<?php
class tvSerieRepository
{
    /** @var PDO $connection */
    public $connection;

    public function __construct() {
        $this->connection = $this->connect();
    }

    public function connect() {
        $dsn = "mysql:host=localhost:3308;dbname=test";
        $user = "root";
        $passwd = "12345";
        return new PDO($dsn, $user, $passwd);
    }

    public function listTvShowByTitle(string $title = null): array
    {
        $query = "SELECT * FROM tv_series inner JOIN tv_series_intervals ON tv_series.id = tv_series_intervals.id_tv_series and tv_series_intervals.show_time = NOW()";

        if ($title) {
            $query .= "and where tv_series.title Like '%". $title . "%'";
        }

        return $this
            ->connect()
            ->query($query)
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>