<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>User Directory — Mockup A</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  :root {
    --paper: #EDEEF0;
    --card: #FFFFFF;
    --ink: #1B1D22;
    --ink-soft: #6B6E76;
    --hairline: #DCDEE3;
    --tab: #3B3A73;

    --hue-1: #E0663F;
    --hue-2: #C98A1F;
    --hue-3: #2E8B7E;
    --hue-4: #4C5FD5;
    --hue-5: #A24C8B;
    --hue-6: #6E7C33;

    --green: #2E8B57;
    --green-soft: #E4F2E9;
    --yellow: #C98A1F;
    --yellow-soft: #FBF0DC;
  }

  * { box-sizing: border-box; }

  body {
    margin: 0;
    background: var(--paper);
    font-family: 'Space Grotesk', sans-serif;
    color: var(--ink);
    padding: 56px 32px 80px;
  }

  .header { max-width: 1080px; margin: 0 auto 40px; }

  .eyebrow {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--tab);
    margin: 0 0 10px;
  }

  h1 {
    font-weight: 600;
    font-size: 30px;
    margin: 0 0 8px;
    letter-spacing: -0.01em;
  }

  .sub { font-size: 14px; color: var(--ink-soft); margin: 0; max-width: 560px; }

  .grid {
    max-width: 1080px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
  }

  @media (max-width: 860px) { .grid { grid-template-columns: repeat(2, 1fr); } }
  @media (max-width: 560px) { .grid { grid-template-columns: 1fr; } body { padding: 40px 18px 60px; } }

  .card {
    position: relative;
    background: var(--card);
    border: 1px solid var(--hairline);
    border-radius: 4px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
  }

  .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 24px -14px rgba(27,29,34,0.35);
    border-color: #C7CAD1;
  }

  .avatar-wrap {
    position: relative;
    flex: 0 0 56px;
    width: 56px;
    height: 56px;
  }

  .avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    position: relative;
    box-shadow: 0 0 0 3px transparent;
    transition: box-shadow 0.15s ease;
  }

  .avatar-placeholder {
    font-family: 'IBM Plex Mono', monospace;
    font-weight: 500;
    font-size: 16px;
    color: #fff;
  }

  .avatar-photo { background-size: cover; background-position: center; }

  .avatar-wrap[data-state="member"] .avatar { box-shadow: 0 0 0 3px var(--green); }
  .avatar-wrap[data-state="leader"] .avatar { box-shadow: 0 0 0 3px var(--yellow); }

  .check-badge {
    position: absolute;
    bottom: -2px;
    right: -2px;
    width: 19px;
    height: 19px;
    border-radius: 50%;
    background: var(--green);
    display: none;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--card);
  }
  .avatar-wrap[data-state="member"] .check-badge { display: flex; background: var(--green); }
  .avatar-wrap[data-state="leader"] .check-badge { display: flex; background: var(--yellow); }

  .check-badge svg { width: 10px; height: 10px; }

  .crown-btn {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 22px;
    height: 22px;
    border-radius: 50%;
    border: 1.5px solid var(--hairline);
    background: var(--card);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    padding: 0;
    transition: all 0.15s ease;
  }
  .crown-btn svg { width: 12px; height: 12px; stroke: var(--ink-soft); fill: none; }
  .crown-btn:hover { border-color: var(--yellow); }
  .avatar-wrap[data-state="leader"] .crown-btn {
    background: var(--yellow);
    border-color: var(--yellow);
  }
  .avatar-wrap[data-state="leader"] .crown-btn svg { stroke: #fff; fill: #fff; }

  .info { min-width: 0; }
  .name {
    font-weight: 500;
    font-size: 16px;
    margin: 0 0 3px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .meta {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    color: var(--ink-soft);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .role-tag {
    font-size: 10px;
    padding: 1px 6px;
    border-radius: 3px;
    background: var(--green-soft);
    color: var(--green);
    display: none;
  }
  .avatar-wrap[data-state="member"] ~ .info .role-tag.tag-member { display: inline-block; }
  .avatar-wrap[data-state="leader"] ~ .info .role-tag.tag-leader {
    display: inline-block;
    background: var(--yellow-soft);
    color: var(--yellow);
  }

  .footer-note {
    max-width: 1080px;
    margin: 34px auto 0;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11.5px;
    color: var(--ink-soft);
  }
</style>
</head>
<body>

  <div class="header">
    <p class="eyebrow">Mockup A — Directory</p>
    <h1>Members</h1>
    <p class="sub">Click a photo to select as member (green). Click the crown to promote to project leader (yellow).</p>
  </div>

  <div class="grid" id="grid"></div>

  <p class="footer-note">Mockup — click avatars and crowns to test the interaction.</p>

<script>
  const users = [
    { name: "Amara Delgado", role: "Designer", photo: true, hue: 1, state: "none" },
    { name: "Kenji Watanabe", role: "Engineer", photo: false, hue: 4, state: "member" },
    { name: "Priya Natarajan", role: "Member", photo: false, hue: 3, state: "none" },
    { name: "Oscar Lindqvist", role: "Engineer", photo: true, hue: 2, state: "leader" },
    { name: "Fatou Diallo", role: "Community lead", photo: false, hue: 5, state: "none" },
    { name: "Milo Bergström", role: "Member", photo: false, hue: 6, state: "none" },
    { name: "Elena Kowalska", role: "Designer", photo: true, hue: 3, state: "none" },
    { name: "Théo Marchetti", role: "Member", photo: false, hue: 1, state: "none" },
    { name: "Nadia Ferreira", role: "Engineer", photo: false, hue: 2, state: "none" },
  ];

  const gradients = {
    1: 'linear-gradient(135deg,#E0663F,#F0A57E)',
    2: 'linear-gradient(135deg,#C98A1F,#E8C570)',
    3: 'linear-gradient(135deg,#2E8B7E,#7FC4B8)',
    4: 'linear-gradient(135deg,#4C5FD5,#8D9AF0)',
    5: 'linear-gradient(135deg,#A24C8B,#D18FC0)',
    6: 'linear-gradient(135deg,#6E7C33,#A6B76C)',
  };

  const checkIcon = '<svg viewBox="0 0 24 24"><path d="M5 12l4 4 10-10" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>';
  const crownIcon = '<svg viewBox="0 0 24 24"><path d="M3 18h18l-1.6-8-4.4 3.2L12 6l-3 7.2L4.6 10 3 18z" stroke-width="1.8" stroke-linejoin="round"/></svg>';

  function initials(name) {
    return name.split(' ').map(p => p[0]).slice(0,2).join('').toUpperCase();
  }

  const grid = document.getElementById('grid');

  users.forEach((u) => {
    const card = document.createElement('div');
    card.className = 'card';

    const avatarWrap = document.createElement('div');
    avatarWrap.className = 'avatar-wrap';
    avatarWrap.dataset.state = u.state;

    const avatar = document.createElement('div');
    avatar.className = 'avatar ' + (u.photo ? 'avatar-photo' : 'avatar-placeholder');
    avatar.style.background = gradients[u.hue];
    if (!u.photo) avatar.textContent = initials(u.name);
    avatar.title = 'Click to select as member';
    avatar.addEventListener('click', () => {
      avatarWrap.dataset.state = avatarWrap.dataset.state === 'none' ? 'member' : 'none';
    });

    const badge = document.createElement('div');
    badge.className = 'check-badge';
    badge.innerHTML = checkIcon;

    const crownBtn = document.createElement('button');
    crownBtn.className = 'crown-btn';
    crownBtn.innerHTML = crownIcon;
    crownBtn.title = 'Set as project leader';
    crownBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      avatarWrap.dataset.state = avatarWrap.dataset.state === 'leader' ? 'member' : 'leader';
    });

    avatarWrap.appendChild(avatar);
    avatarWrap.appendChild(badge);
    avatarWrap.appendChild(crownBtn);

    const info = document.createElement('div');
    info.className = 'info';
    info.innerHTML = `
      <p class="name">${u.name}</p>
      <p class="meta">
        ${u.role}
        <span class="role-tag tag-member">member</span>
        <span class="role-tag tag-leader">leader</span>
      </p>`;

    card.appendChild(avatarWrap);
    card.appendChild(info);
    grid.appendChild(card);
  });
</script>

</body>
</html>