use ilias;

insert into xvin_exercise(id, title, question) values
	(1, "test1", "???"),
    (2, "test2", "???"),
    (3, "test3", "???");
insert into xvin_exercise_seq(sequence) values
	(3);
insert into xvin_interview(id, title) values
	(1, "test1"),
    (2, "test2"),
    (3, "test3");
insert into xvin_interview_seq(sequence) values
	(3);
insert into xvin_exercise_ref(id, exercise_id, video_interview_id) values
	(1, 1, 1),
    (2, 1, 2),
    (3, 1, 3);
insert into xvin_exercise_ref_seq(sequence) values
	(3);
