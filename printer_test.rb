#IO.popen('lpr -P Brother_QL-500_server output.pdf') { |io| while (line = io.gets) do puts line end }
IO.popen('lpr -P QL-720NW output.pdf') { |io| while (line = io.gets) do puts line end }
