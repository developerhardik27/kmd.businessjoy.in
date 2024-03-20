--
-- Table structure for table `leadstatus_name`
--

CREATE TABLE `leadstatus_name` (
  `id` int(11) NOT NULL,
  `leadstatus_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leadstatus_name`
--

INSERT INTO `leadstatus_name` (`id`, `leadstatus_name`) VALUES
(1, 'Not Interested'),
(2, 'Not Receiving'),
(3, 'New Lead'),
(4, 'Interested'),
(5, 'Switch Off'),
(6, 'Does Not Exist'),
(7, 'Email Sent'),
(8, 'Wrong Number'),
(9, 'By Mistake'),
(10, 'Positive'),
(11, 'Busy'),
(12, 'Call Back'),
(13, 'Not Interested'),
(14, 'Not Receiving'),
(15, 'New Lead'),
(16, 'Interested'),
(17, 'Switch Off'),
(18, 'Does Not Exist'),
(19, 'Email Sent'),
(20, 'Wrong Number'),
(21, 'By Mistake'),
(22, 'Positive'),
(23, 'Busy'),
(24, 'Call Back');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `leadstatus_name`
--
ALTER TABLE `leadstatus_name`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leadstatus_name`
--
ALTER TABLE `leadstatus_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;



-- 2nd new table 


--
-- Table structure for table `leadstage`
--

CREATE TABLE `leadstage` (
  `id` int(11) NOT NULL,
  `leadstage_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leadstage`
--

INSERT INTO `leadstage` (`id`, `leadstage_name`) VALUES
(1, 'New Lead'),
(2, 'Requirement Gathering'),
(3, 'Quotation'),
(4, 'In Followup'),
(5, 'Sale'),
(6, 'Cancelled'),
(7, 'Disqualified');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `leadstage`
--
ALTER TABLE `leadstage`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `leadstage`
--
ALTER TABLE `leadstage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;