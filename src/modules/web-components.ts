import { setup as setupCommentSection } from "../components/comment-section";
import { setup as setupLiteYouTubeEmbed } from "../components/lite-youtube-embed";

export function install() {
  setupLiteYouTubeEmbed();
  setupCommentSection();
}
