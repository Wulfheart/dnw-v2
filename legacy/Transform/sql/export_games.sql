SELECT *
FROM wD_Games g
WHERE g.phase = 'Finished'
  AND variantID = 1
  AND id NOT IN (SELECT gameID
                 FROM `wD_MovesArchive`
                 WHERE orderID = 0)
